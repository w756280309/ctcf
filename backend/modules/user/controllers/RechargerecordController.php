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
use common\service\AccountService;
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
        if ($type === User::USER_TYPE_PERSONAL) {
            $query = (new \yii\db\Query)    //连表查询,获取充值记录银行名称
                ->select("$r.*, $u.bank_name")
                ->from($r)
                ->innerJoin($u, "$r.bank_id = $u.id")
                ->where(["$r.uid" => $id]);
        } else {
            $m = MoneyRecord::tableName();
            $query = (new \yii\db\Query)    //连表查询,获取充值记录银行名称
                ->select("$r.*, $u.bank_name, ($m.balance - $m.in_money) balance")
                ->from($r)
                ->innerJoin($u, "$r.bank_id = $u.id")
                ->innerJoin($m, "$m.osn = $r.sn")
                ->where(["$r.uid" => $id]);
        }

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

    //修复充值数据
    public function actionRepairData($sn)
    {
        $record = RechargeRecord::find()->where(['sn' => $sn])->one();
        $status = intval($record->status);
        if (is_null($record)) {
            return ['success' => false, 'message' => '没有找到充值记录'];
        }
        if ($status === RechargeRecord::STATUS_YES) {
            return ['success' => false, 'message' => '成功记录不需要修复'];
        }
        if ($record->created_at < strtotime('-10 day')) {
            return ['success' => false, 'message' => '只能修复10天内的订单'];
        }
        $moneyRecord = MoneyRecord::find()->where(['osn' => $sn])->one();
        if (!is_null($moneyRecord)) {
            return ['success' => false, 'message' => '成功记录不需要修复'];
        }
        $res = Yii::$container->get('ump')->getRechargeInfo($sn, $record->created_at);
        if ($res->isSuccessful()) {
            $tran_state = intval($res->get('tran_state'));
            if ($tran_state === 2) {
                $res = (new AccountService())->confirmRecharge($record);
                if ($res) {
                    return ['success' => true, 'message' => '成功修复数据'];
                } else {
                    return ['success' => false, 'message' => '数据修复失败'];
                }
            } else {
                $return_message = [0 => '初始', 2 => '成功', 3 => '失败', 4 => '不明', 5 => '交易关闭'];
                if (key_exists($tran_state, $return_message)) {
                    return ['success' => false, 'message' => '联动状态：' . $return_message[$tran_state]];
                }
            }
        }
        return ['success' => false, 'message' => '联动状态异常'];
    }
}