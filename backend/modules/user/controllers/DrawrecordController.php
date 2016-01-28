<?php

namespace backend\modules\user\controllers;

use Yii;
use backend\controllers\BaseController;
use yii\data\Pagination;
use common\models\user\DrawRecord;
use common\models\user\DrawRecordTime;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\user\User;
use common\lib\bchelp\BcRound;
use common\models\user\UserBank;
use common\models\user\Batchpay;
use yii\web\Response;
use common\utils\TxUtils;
use common\models\sms\SmsMessage;
use common\models\draw\DrawManager;
use common\models\draw\DrawException;

class DrawrecordController extends BaseController
{
    /**
     * 提现流水明细
     */
    public function actionDetail($id = null, $type = null)
    {
        //提现明细页面的搜索功能
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');
        $query = DrawRecordTime::find()->where(['uid' => $id]);
        if ($type == User::USER_TYPE_PERSONAL) {
            if ($status === '-1') {
                $query->andWhere(['status' => 0]);
            } elseif (!empty($status)) {
                $query->andWhere(['status' => $status]);
            }
        }

        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time.' 23:59:59')]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time.' 0:00:00')]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->asArray()->all();

        $user = User::find()->where(['id' => $id])->select('username,org_name')->one();

        $moneyTotal = 0;  //提现总额
        $successNum = 0;  //成功笔数
        $failureNum = 0;  //失败笔数
        $numdata = DrawRecordTime::find()->where(['uid' => $id])->select('money,status')->asArray()->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($numdata as $data) {
            if ($data['status'] == DrawRecordTime::STATUS_SUCCESS) {
                $moneyTotal = bcadd($moneyTotal, $data['money']);
                ++$successNum;
            } elseif ($data['status'] == DrawRecordTime::STATUS_FAIL) {
                ++$failureNum;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal, 2);

        //渲染到静态页面
        return $this->render('list', [
                    'type' => $type,
                    'id' => $id,
                    'model' => $model,
                    'pages' => $pages,
                    'user' => $user,
                    'moneyTotal' => $moneyTotal,
                    'successNum' => $successNum,
                    'failureNum' => $failureNum,
        ]);
    }

    private function alert($res, $msg, $tourl = null)
    {
        $this->alert = $res;
        $this->msg = $msg;
        if (null !== $tourl) {
            $this->toUrl = $tourl;
        }
    }

    //录入提现数据
    public function actionEdit($id = null, $type = null)
    {
        $banks = Yii::$app->params['bank'];
        $bankInfo = [];
        foreach ($banks as $k => $v) {
            $bankInfo[$k] = $v['bankname'];
        }
        $model = new DrawRecordTime();
        $model->uid = $id;
        $model->created_at = strtotime(Yii::$app->request->post('created_at'));
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $userAccountInfo = UserAccount::findOne(['uid' => $id, 'type' => UserAccount::TYPE_BORROW]);//融资账户;
            if (bccomp($userAccountInfo->available_balance, $model->money) < 0) {
                $this->alert(2, '可用余额不足');
            } else {
                $money = $model->money;
                $model->status = DrawRecord::STATUS_ZERO;
                $bc = new BcRound();
                bcscale(14); //设置小数位数
                $userAccountInfo->available_balance = $bc->bcround(bcsub($userAccountInfo->available_balance, $money), 2);
                $userAccountInfo->out_sum = $bc->bcround(bcadd($userAccountInfo->out_sum, $money), 2);
                $userAccountInfo->freeze_balance = $bc->bcround(bcadd($userAccountInfo->freeze_balance, $money), 2);
                $userAccountInfo->drawable_balance = $bc->bcround(bcsub($userAccountInfo->drawable_balance, $money), 2);

                $money_record = new MoneyRecord();
                $money_record->sn = TxUtils::generateSn("MR");
                $money_record->type = MoneyRecord::TYPE_DRAW;
                $money_record->osn = $model->sn;
                $money_record->account_id = $userAccountInfo->id;
                $money_record->uid = $id;
                $money_record->balance = $userAccountInfo->available_balance;
                $money_record->out_money = $money;

                if ($model->save() && $userAccountInfo->save() && $money_record->save()) {
                    $this->alert(1, '操作成功', "detail?id=$id&type=$type");
                } else {
                    $this->alert(2, '操作失败');
                }
            }
        }

        return $this->render('edit', [
                    'banks' => $bankInfo,
                    'type' => $type,
                    'id' => $id,
                    'model' => $model,
        ]);
    }

    public function actionDrawexamin()
    {
        $id = Yii::$app->request->post('id');
        $uid = Yii::$app->request->post('uid');
        $status = (int) Yii::$app->request->post('status');
        $res = 0;
        $msg = '操作失败';
        $draw = DrawRecord::findOne($id);
        if (null !== $draw && in_array($status, [DrawRecord::STATUS_SUCCESS,DrawRecord::STATUS_FAIL])) {
            $userAccountInfo = UserAccount::findOne(['uid' => $draw->uid, 'type' => UserAccount::TYPE_BORROW]); //融资账户;
            $bc = new BcRound();
            $money = $draw->money;
            $YuE = $userAccountInfo->available_balance;
            $money_type = 0;
            bcscale(14); //设置小数位数
            if ($status === DrawRecord::STATUS_SUCCESS) {
                $userAccountInfo->account_balance = $bc->bcround(bcsub($userAccountInfo->account_balance, $money), 2);
                $userAccountInfo->freeze_balance = $bc->bcround(bcsub($userAccountInfo->freeze_balance, $money), 2);
                $money_type = MoneyRecord::TYPE_DRAW_SUCCESS;
            } elseif ($status === DrawRecord::STATUS_FAIL) {
                $userAccountInfo->available_balance = $bc->bcround(bcadd($userAccountInfo->available_balance, $money), 2);
                $userAccountInfo->in_sum = $bc->bcround(bcadd($userAccountInfo->in_sum, $money), 2);
                $userAccountInfo->freeze_balance = $bc->bcround(bcsub($userAccountInfo->freeze_balance, $money), 2);
                $userAccountInfo->drawable_balance = $bc->bcround(bcadd($userAccountInfo->drawable_balance, $money), 2);
                $money_type = MoneyRecord::TYPE_DRAW_RETURN;
            }

            $moneyInfo = new MoneyRecord();
            // 生成一个SN流水号
            $sn = TxUtils::generateSn("MR");
            $moneyInfo->uid = $uid;
            $moneyInfo->sn = $sn;
            $moneyInfo->osn = $draw->sn;
            $moneyInfo->type = $money_type;
            $moneyInfo->balance = $YuE;
            if($status == DrawRecord::STATUS_SUCCESS){
                $moneyInfo->out_money = $money;
                $draw->status = DrawRecord::STATUS_SUCCESS;
            }else{
                $moneyInfo->in_money = $money;
                $draw->status = DrawRecord::STATUS_FAIL; //驳回
            }
            $moneyInfo->account_id = $userAccountInfo->id;

            //开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if (($draw->save()) && ($moneyInfo->save()) && ($userAccountInfo->save())) {
                $transaction->commit();
                $msg = '操作成功';
                $res = 1;
            } else {
                $transaction->rollBack();
            }
        } else {
            $msg = '无法找到';
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['res' => $res, 'msg' => $msg, 'data' => ''];
    }

    /*
     * 会员管理 提现申请页面
     */
    public function actionApply()
    {
        $query = User::find();
        $request = Yii::$app->request->get();

        if (!empty($request['name'])) {
            $query->andFilterWhere(['like', 'real_name', $request['name']]);
        }
        if (!empty($request['mobile'])) {
            $query->andFilterWhere(['like', 'mobile', $request['mobile']]);
        }

        $tzUser = $query->andWhere('type=1')->asArray()->all();
        $res = [];
        foreach ($tzUser as $k => $v) {
            $res[$v['id']] = $v;
        }
        $arr = [];
        foreach ($tzUser as $k => $v) {
            $arr[] = $v['id'];
        }

        $draw = DrawRecord::find()->where(['in', 'uid', $arr]);
        if (!empty($request['starttime'])) {
            $draw->andFilterWhere(['>=', 'created_at', strtotime($request['starttime'])]);
        }
        if (!empty($request['endtime'])) {
            $draw->andFilterWhere(['<=', 'created_at', strtotime($request['endtime']) + 24 * 60 * 60]);
        }

        $pages = new Pagination(['totalCount' => $draw->count(), 'pageSize' => '10']);
        $model = $draw->offset($pages->offset)->limit($pages->limit)->orderBy('created_at DESC')->all();

        return $this->render('apply', [
            'res' => $res,
            'model' => $model,
            'category' => 1,
            'pages' => $pages,
            'request' => $request,
        ]);
    }

    /**
     * 审核界面，弹框.
     */
    public function actionExaminfk($pid, $id)
    {
        $this->layout = false;

        $userBank = UserBank::find()->where(['uid' => $pid])->one();
        $model = DrawRecord::findOne($id);
        $tixianUserInfo = User::findOne(['type' => User::USER_TYPE_PERSONAL, 'id' => $model->uid]);

        return $this->render('examinfk', ['model' => $model, 'tixianSq' => $tixianUserInfo, 'userBank' => $userBank]);
    }

    // 点击后审核通过或不通过
    public function actionChecksq()
    {
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');

        if (empty($id) || empty($type)) {
            return false;
        }
        $model = DrawRecord::findOne($id);
        try {
            $draw = DrawManager::audit($model, $type);
            $user = $draw->user;
            $mess = [
                $user->real_name,
                date('Y-m-d H:i:s', $model->created_at),
                number_format($model->money, 2),
                Yii::$app->params['contact_tel'],
            ];
            $sms = new SmsMessage([
                'uid' => $model->uid,
                'mobile' => $user->mobile,
                'message' => json_encode($mess),
                'level' => SmsMessage::LEVEL_LOW,
            ]);

            if (DrawRecord::STATUS_DENY === (int) $type) {
                $sms->template_id = Yii::$app->params['sms']['tixian_err'];
                $sms->save();
            } elseif (DrawRecord::STATUS_EXAMINED === (int) $type) {
                $sms->template_id = Yii::$app->params['sms']['tixian_succ'];
                $sms->save();
            }
            return true;
        } catch (DrawException $ex) {
            return false;
        }

    }

    /**
     * 点击放款后开始放款.
     *
     * @return bool
     */
    public function actionChecksqfangkuan()
    {
        $id = Yii::$app->request->post('id');
        $uid = Yii::$app->request->post('uid');
        if (empty($id) || empty($uid)) {
            return false;
        }
        $drawRord = DrawRecord::findOne($id);
        if ($drawRord === null || $drawRord->status != DrawRecord::STATUS_EXAMINED) {
            return false;
        }
        if ($id) {
            $batchPay = new Batchpay();
            $res_bat = $batchPay->singleInsert($this->admin_id, $id);
            if ($res_bat) {
                $drawRord->status = DrawRecord::STATUS_LAUNCH_BATCHPAY;//发起批量代付
                return $drawRord->save();
            }
        }

        return false;
    }

    //点击放款
    public function actionFangkuan($pid = null, $id = null)
    {
        $this->layout = false;
        $money = Yii::$app->request->get('money');
        $name = Yii::$app->request->get('name');
        $id = Yii::$app->request->get('id');

        return $this->render('examinfk_1', ['id' => $id, 'uid' => $pid, 'money' => $money, 'name' => $name]);
    }
}
