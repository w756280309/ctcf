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

class DrawrecordController extends BaseController {

    public function actionDetail($id = null, $type = null) {
        //提现明细页面的搜索功能
        //$query = "uid=$id";
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');
        $query = DrawRecordTime::find()->where(['uid' => $id]);
        if ($type == User::USER_TYPE_PERSONAL && !empty($status)) {
            $query->andWhere(['status' => $status]);
        } 
        
        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time . " 23:59:59")]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time . " 0:00:00")]);
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
        foreach ($numdata as $data){
            $moneyTotal = bcadd($moneyTotal,$data['money']);
            if($data['status']==DrawRecordTime::STATUS_SUCCESS){
                $successNum++;
            }else if($data['status']==DrawRecordTime::STATUS_FAIL){
                $failureNum++;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal,2);

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

    //录入提现数据
    public function actionEdit($id = null, $type = null) {
        $banks = Yii::$app->params['bank'];
        $bankInfo = [];
        foreach ($banks as $k => $v) {
            $bankInfo[$k] = $v['bankname'];
        }
        $model = new DrawRecordTime();
        $model->uid = $id;
        $model->created_at = strtotime(Yii::$app->request->post("created_at"));
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $money = $model->money;
            //先是在draw_record表上填写流水记录，若添加成功同时往money_record表中更新数据也成功，就准备向user_account表中更新数据，
            $userAccountInfo = $id ? UserAccount::find()->where("uid = $id and type=$type")->one() : (new UserAccount());
            $bc = new BcRound();
            bcscale(14); //设置小数位数
            //账户余额减去提现的钱
            $YuE = $userAccountInfo->account_balance = $bc->bcround(bcsub($userAccountInfo->account_balance, $money), 2);
            //可用余额减去提现的钱
            $userAccountInfo->available_balance = $bc->bcround(bcsub($userAccountInfo->available_balance, $money), 2);
            $userAccountInfo->out_sum = $bc->bcround(bcadd($userAccountInfo->out_sum, $money), 2);

            $moneyInfo = new MoneyRecord();
            // 生成一个SN流水号
            $sn = $model::createSN();
            $moneyInfo->uid = $id;
            $moneyInfo->sn = $sn;
            $moneyInfo->type = 1;
            $moneyInfo->balance = $YuE;
            $moneyInfo->out_money = $money;
            $res = UserAccount::find()->select("id")->where("uid=$id and type=$type")->asArray()->one();
            $model->account_id = $moneyInfo->account_id = $res['id'];
            if ($model->validate() === false) {
                exit();
            }
            //开启事务
            $transaction = Yii::$app->db->beginTransaction();
            $model->status = 2;
            if (($model->save()) && ($moneyInfo->save()) && ($userAccountInfo->save())) {
                $transaction->commit();
                $this->alert = 1;
                $this->msg = "操作成功";
                $this->toUrl = "detail?id=$id&type=$type";
            } else {
                $transaction->rollBack();
                exit("failure");
            }
        }

        return $this->render('edit', [
                    'banks' => $bankInfo,
                    'type' => $type,
                    'id' => $id,
                    'model' => $model,
        ]);
    }

    /*
     * 会员管理 提现申请页面
     */
    public function actionApply($name = null, $mobile = null) {
        $query = User::find();
        
        if(!empty($name)) {
            $query->andFilterWhere(['like','real_name',$name]);
        }        
        if(!empty($mobile)) {
            $query->andFilterWhere(['like','mobile',$mobile]);
        }
        
        $tzUser = $query->andWhere("type=1")->select('id,usercode,mobile,real_name')->asArray()->all();
        $res = [];
        foreach ($tzUser as $k => $v) {
            $res[$v['id']] = $v;
        }
        $arr = [];
        foreach ($tzUser as $k => $v) {
            $arr[] = $v['id'];
        }
        $model = DrawRecord::find()->where(['in', 'uid', $arr]);
        $pages = new Pagination(['totalCount' => $model->count(), 'pageSize' => '10']);
        $model = $model->offset($pages->offset)->limit($pages->limit)->orderBy('created_at DESC')->all();
        return $this->render('apply', [
                    'res' => $res,
                    'model' => $model,
                    'category' => 1,
                    'pages' => $pages
        ]);
    }

    /**
     * 审核界面，弹框
     * @param type $pid
     */
    public function actionExaminfk($pid = null, $id = null) {
        $this->layout = false;
        
        $userBank = UserBank::find()->where(['uid' => $pid])->one();
        $model = DrawRecord::findOne($id);
        $tixianUserInfo = User::findOne(['type' => User::USER_TYPE_PERSONAL, 'id' => $model->uid]);
        
        return $this->render('examinfk', ['model' => $model, 'tixianSq' => $tixianUserInfo, 'userBank' => $userBank]);
    }

    // 点击后审核通过或不通过
    public function actionChecksq() {
        $id = Yii::$app->request->post("id");
        $type = Yii::$app->request->post("type");

        if(empty($id) || empty($type)) {
            return false;
        }
        
        $model = DrawRecord::findOne($id);
        $model->status = $type;
        if ($model->save()) {
            return true;
        }

        return false;
    }

    // 点击放款后开始放款
    public function actionChecksqfangkuan() {
        $id = Yii::$app->request->post("id");
        $uid = Yii::$app->request->post("uid");
        $model = DrawRecord::findOne($id);
        if($model->status==DrawRecord::STATUS_ZERO||$model->status==DrawRecord::STATUS_SUCCESS){
            return false;
        }
        if ($id) {
            $bc = new BcRound();
            bcscale(14); //设置小数位数
            $drawRord = DrawRecord::findOne($id);
            $money = $drawRord->money;
            $userAccount = UserAccount::find()->where("uid = " . $uid)->one();
            //放款后，账户余额要减去money
            $YuE = $userAccount->account_balance = $bc->bcround(bcsub($userAccount->account_balance, $money), 2);
            //冻结金额减去money
            $userAccount->freeze_balance = $bc->bcround(bcsub($userAccount->freeze_balance, $money), 2);
            //账户出金总额
            $userAccount->out_sum = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);

            $momeyRecord = new MoneyRecord();
            // 生成一个SN流水号
            $sn = $momeyRecord::createSN();
            $momeyRecord->uid = $id;
            $momeyRecord->sn = $sn;
            $momeyRecord->type = 1;
            $momeyRecord->balance = $YuE;
            $momeyRecord->out_money = $money;
            $res = UserAccount::find()->select("id")->where("uid=" . $uid)->asArray()->one();
            $momeyRecord->account_id = $res['id'];
            //开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if ($momeyRecord->save() && $userAccount->save()) {
                $drawRord->status = 2;
                $drawRord->save();
                $transaction->commit();
                return true;
            } else {
                $transaction->rollBack();
                exit("failure");
            }
        }
        return false;
    }

    //点击放款
    public function actionFangkuan($pid = null, $id = null) {
        $this->layout = false;
        $money = Yii::$app->request->get("money");
        $name = Yii::$app->request->get("name");
        $id = Yii::$app->request->get("id");

        return $this->render('examinfk_1', ['id' => $id, 'uid' => $pid, 'money' => $money, 'name' => $name]);
    }

}
