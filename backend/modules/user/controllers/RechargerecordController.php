<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\models\bank\Bank;
use common\models\epay\EpayUser;
use common\models\user\MoneyRecord;
use common\models\user\RechargeRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use Yii;
use yii\data\Pagination;
use yii\log\FileTarget;
use yii\web\Response;

class RechargerecordController extends BaseController
{
    /**
     * 投资会员充值流水明细.
     *
     * @param int $id    用户ID
     * @param int $type  用户类型
     */
    public function actionDetail($id, $type)
    {
        if (empty($id) || empty($type) || !in_array($type, [1, 2])) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $type = intval($type);
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');

        $r = RechargeRecord::tableName();
        $u = UserBanks::tableName();
        $query = (new \yii\db\Query)    //连表查询,获取充值记录银行名称
            ->select("$r.*, $u.bank_name")
            ->from($r)
            ->leftJoin($u, "$r.bank_id = $u.id")
            ->where(["$r.uid" => $id]);

        // 状态
        if (!empty($status)) {
            if ('3' === substr($status, 0, 1)) {
                $query->andWhere(["$r.status" => substr($status, 1, 1), 'pay_type' => RechargeRecord::PAY_TYPE_POS]);
            } else {
                $query->andWhere(["$r.status" => substr($status, 1, 1), 'pay_type' => [RechargeRecord::PAY_TYPE_QUICK, RechargeRecord::PAY_TYPE_NET]]);
            }
        }

        // 时间
        if (!empty($time)) {
            $query->andFilterWhere(['<', "$r.created_at", strtotime($time.' 23:59:59')]);
            $query->andFilterWhere(['>=', "$r.created_at", strtotime($time.' 0:00:00')]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        $arr = [];
        foreach ($model as $key => $val) {
            if (RechargeRecord::PAY_TYPE_NET === (int)$val['pay_type']) {
                $arr[$key] = $val['bank_id'];
            }
        }

        if (!empty($arr)) {
            $bank = Bank::findAll(['id' => $arr]);       //获取所有通过网银充值用户的充值银行卡信息

            foreach ($arr as $key => $val) {      //替换前台显示内容
                foreach ($bank as $v) {
                    if ((int)$arr[$key] === $v['id']) {
                        $model[$key]['bank_name'] = $v['bankName'];
                    }
                }
            }
        }

        //取出用户
        $user = User::findOne(['id' => $id]);

        $moneyTotal = 0;  //取出充值金额总计，应该包括充值成功的和充值失败的
        $successNum = 0;  //充值成功笔数
        $failureNum = 0;  //充值失败笔数
        $numdata = RechargeRecord::find()->where(['uid' => $id, 'status' => [1, 2]])->select('fund,status')->asArray()->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($numdata as $data) {
            if ($data['status'] == 1) {
                $moneyTotal = bcadd($moneyTotal, $data['fund']);
                ++$successNum;
            } else {
                ++$failureNum;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal, 2);

        //查看当前用户账户信息
        $userAccount = UserAccount::find()->where(['uid' => $id])->one();
        $available_balance = $userAccount ? number_format($userAccount->available_balance, 2) : 0;
        //获取联动用户信息
        $ePayUser = EpayUser::find()->where(['appUserId' => $id])->one();
        $user_account = 0;
        if ($ePayUser && $ePayUser['epayUserId']) {
            $ump = Yii::$container->get('ump');

            if (1 === $type) {
                $res = $ump->getUserInfo($ePayUser['epayUserId']);
            } else {
                $res = $ump->getMerchantInfo($ePayUser['epayUserId']);
            }

            if ($res->isSuccessful()) {
                $account = $res->get('balance');//以分为单位
                if ($account) {
                    $user_account = number_format($account / 100, 2);
                }
            }
        }

        //渲染到静态页面
        return $this->render('list', [
            'uid' => $id,
            'type' => $type,
            'status' => $status,
            'time' => $time,
            'model' => $model,
            'pages' => $pages,
            'user' => $user,
            'moneyTotal' => $moneyTotal,
            'successNum' => $successNum,
            'failureNum' => $failureNum,
            'available_balance' => $available_balance,
            'user_account' => $user_account,
        ]);
    }

    /**
     * 获取指定充值订单在联动的状态
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionGetOrderStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (Yii::$app->request->isGet) {
            $sn = Yii::$app->request->get('sn');
            $rechargeRecord = RechargeRecord::find()->where(['sn' => $sn])->one();
            if ($rechargeRecord) {
                $res = Yii::$container->get('ump')->getRechargeInfo($rechargeRecord['sn'], $rechargeRecord['created_at']);
                if ($res->isSuccessful()) {
                    $tran_state = intval($res->get('tran_state'));
                    //0初始,2成功,3失败,4不明,5交易关闭
                    $return_message = [0 => '初始', 2 => '成功', 3 => '失败', 4 => '不明', 5 => '交易关闭'];
                    if ($tran_state >= 0 && key_exists($tran_state, $return_message)) {
                        return ['code' => true, 'message' => $return_message[$tran_state]];
                    }
                    return ['code' => false, 'message' => '返回信息不明确'];
                }
                return ['code' => false, 'message' => '['.$res->get('ret_code').']'.$res->get('ret_msg')];
            }
            return ['code' => false, 'message' => '订单不存在'];
        } else {
            return ['code' => false, 'message' => '非法请求'];
        }
    }

    public function actionLog()
    {
        $ar = array(
            'auth' => array(
                'user' => 'customer',
                'password' => 'password',
                'context' => '4',
            ),
            'owner' => array(
                'user' => 'customer2',
                'context' => '4',
            ),
            'language' => 'en',
            'task' => array(
                'code' => '0130',
            ),
        );
        $logs = new \common\lib\log\Logs();
        echo $logs->createXml($ar, 1);
        exit;
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath().'/logs/zhy.log';
        $log->messages[] = ['test', 2, 'application', $time]; //第二个值 1 error 2 warning

        $log->export();
    }

    public function actionRechargeSh()
    {
        $res = 0;
        $op = Yii::$app->request->post('op');
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');
        $uid = Yii::$app->request->post('uid');

        if ($op == 'status') {
            //项目状态
            $recharge = RechargeRecord::findOne($id);
            if ($type == 1) {
                //开启事务
                $transaction = Yii::$app->db->beginTransaction();
                $money = $recharge->fund;
                //先是在recharge_record表上填写流水记录，若添加成功同时往money_record表中更新数据也成功，就准备向user_account表中更新数据，
                $userAccountInfo = UserAccount::findOne(['uid' => $recharge->uid, 'type' => UserAccount::TYPE_BORROW]);//融资账户

                $bc = new BcRound();
                bcscale(14); //设置小数位数
                //充值时不会冻结资金,账户余额加上充值的钱
                $YuE = $userAccountInfo->account_balance = $bc->bcround(bcadd($userAccountInfo->account_balance, $money), 2);
                //可用余额加上充值的钱
                $userAccountInfo->available_balance = $bc->bcround(bcadd($userAccountInfo->available_balance, $money), 2);
                //账户入户总金额也要加上充值的钱
                $userAccountInfo->in_sum = $bc->bcround(bcadd($userAccountInfo->in_sum, $money), 2);
                //融资人的审核之后可提现金额增加
                $userAccountInfo->drawable_balance = $bc->bcround(bcadd($userAccountInfo->drawable_balance, $money), 2);
                $moneyInfo = new MoneyRecord();
                // 生成一个SN流水号
                $sn = MoneyRecord::createSN();
                $moneyInfo->uid = $uid;
                $moneyInfo->sn = $sn;
                $moneyInfo->type = 0;
                $moneyInfo->balance = $YuE;
                $moneyInfo->in_money = $money;
                $moneyInfo->account_id = $userAccountInfo->id;
                $recharge->status = RechargeRecord::STATUS_YES;
                if (($moneyInfo->save()) && ($userAccountInfo->save()) && $recharge->save(false)) {
                    $transaction->commit();
                    $res = $this->alert = 1;
                    $this->msg = '操作成功';
                    $this->toUrl = "detail?id=$id&type=$type";
                } else {
                    $transaction->rollBack();
                    $this->alert = 2;
                    $this->msg = '操作失败';
                }
            } else {
                $recharge->status = RechargeRecord::STATUS_FAULT;
                $recharge->save(false);
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['res' => $res, 'msg' => '', 'data' => ''];
    }
}