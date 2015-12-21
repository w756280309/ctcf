<?php

namespace backend\modules\user\controllers;

use Yii;
use yii\data\Pagination;
use yii\web\Response;
use backend\controllers\BaseController;
use common\models\user\RechargeRecordTime;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\user\User;
use common\lib\bchelp\BcRound;
use yii\log\FileTarget;

class RechargerecordController extends BaseController {

    public function actionDetail($id = null, $type = null) {
        //\Yii::beginProfile('myBenchmark');
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');
        $query = RechargeRecordTime::find()->where(['uid' => $id]);

        // 状态
        if (!empty($status)) {
            $query->andWhere(['status' => $status]);
        }

        // 时间
        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time . " 23:59:59")]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time . " 0:00:00")]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        //取出用户
        $user = User::find()->where(['id' => $id])->select('username,org_name')->one();
        
        $moneyTotal = 0;  //取出充值金额总计，应该包括充值成功的和充值失败的
        $successNum = 0;  //充值成功笔数
        $failureNum = 0;  //充值失败笔数
        $numdata = RechargeRecordTime::find()->where(['uid' => $id, 'status' => [1,2]])->select('fund,status')->asArray()->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($numdata as $data){
            $moneyTotal = bcadd($moneyTotal,$data['fund']);
            if($data['status']==1){
                $successNum++;
            }else{
                $failureNum++;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal,2);
//        //充值成功的次数
//        $successNum = RechargeRecordTime::find()->where(['uid' => $id, 'status' => 1])->count('id');
//        //充值失败的次数
//        $failureNum = RechargeRecordTime::find()->where(['uid' => $id, 'status' => 2])->count('id');
       // \Yii::endProfile('myBenchmark');
        //渲染到静态页面
        return $this->render('list', [
                    'type' => $type,
                    'model' => $model,
                    'pages' => $pages,
                    'user' => $user,
                    'moneyTotal' => $moneyTotal,
                    'successNum' => $successNum,
                    'failureNum' => $failureNum,
        ]);
    }

    //录入充值数据
    public function actionEdit($id = null, $type = null) {
        $banks = Yii::$app->params['bank'];
        $bankInfo = ['' => '--请选择--'];
        foreach ($banks as $k => $v) {
            $bankInfo[$k] = $v['bankname'];
        }
        $model = new RechargeRecordTime();
        $model->uid = $id;
        $model->created_at = strtotime(Yii::$app->request->post("created_at"));
        $request = Yii::$app->request->post();
        if($request && empty($model->created_at)) {
            $this->alert = 1;
            $this->msg = '充值时间不能为空';
            return $this->render('edit', [
                   'banks' => $bankInfo,
                   'type' => $type,
                   'id' => $id,
                   'model' => $model,
            ]);
        }
        
        if ($model->load($request) && $model->validate()) {
            $money = $model->fund;
            //先是在recharge_record表上填写流水记录，若添加成功同时往money_record表中更新数据也成功，就准备向user_account表中更新数据，
            $userAccountInfo = $id ? UserAccount::find()->where("uid = $id and type=$type")->one() : (new UserAccount());
            $bc = new BcRound();
            bcscale(14); //设置小数位数
            //充值时不会冻结资金,账户余额加上充值的钱
            $YuE = $userAccountInfo->account_balance = $bc->bcround(bcadd($userAccountInfo->account_balance, $money), 2);
            //可用余额加上充值的钱
            $userAccountInfo->available_balance = $bc->bcround(bcadd($userAccountInfo->available_balance, $money), 2);
            //账户入户总金额也要加上充值的钱
            $userAccountInfo->in_sum = $bc->bcround(bcadd($userAccountInfo->in_sum, $money), 2);

            $moneyInfo = new MoneyRecord();
            // 生成一个SN流水号
            $sn = $model::createSN();
            $moneyInfo->uid = $id;
            $moneyInfo->sn = $sn;
            $moneyInfo->type = 0;
            $moneyInfo->balance = $YuE;
            $moneyInfo->in_money = $money;

            $res = UserAccount::find()->select("id")->where("uid=$id and type=$type")->asArray()->one();
            $model->account_id = $moneyInfo->account_id = $res['id'];
            $model->validate(); //给recharge_record加上account_id字段后，再验证
            //$c = $userAccountInfo->save();
            //开启事务
            $transaction = Yii::$app->db->beginTransaction();
            if (($model->save()) && ($moneyInfo->save()) && ($userAccountInfo->save())) {
                $transaction->commit();
                $this->alert = 1;
                $this->msg = "操作成功";
                $this->toUrl = "detail?id=$id&type=$type";
            } else {
                $transaction->rollBack();
                //exit("failure");
                $this->alert=2;
                $this->msg="操作失败";
            }
        }
        return $this->render('edit', [
                    'banks' => $bankInfo,
                    'type' => $type,
                    'id' => $id,
                    'model' => $model,
        ]);
    }

    public function actionLog() {
        $ar=array
        ( 
            "auth"=> array 
            (
                "user"=>  "customer" ,
                "password"=>  "password" ,
                "context"=>  "4" 
            ) ,
            "owner"=> array 
            ( 
                "user"=>  "customer2" ,
                "context"=>  "4" 
            ) ,
            "language"=>  "en" ,
            "task"=> array
            ( 
                "code"=>  "0130" 
            ) 
        );
        $logs = new \common\lib\log\Logs();
        echo $logs->createXml($ar,1);
        exit;
        $time = microtime(true);
        $log = new FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/zhy.log';
        $log->messages[] = ['test', 2, 'application', $time]; //第二个值 1 error 2 warning
        
        $log->export();
    }
    
    public function actionRechargeSh() {
        $res = 0;
        $op = Yii::$app->request->post('op');
        $id = Yii::$app->request->post('id');
        $type = Yii::$app->request->post('type');
        
        if ($op == 'status') {//项目状态
            $recharge = RechargeRecordTime::findOne($id);
            if($type == 1) {
                $recharge->status = RechargeRecordTime::STATUS_YES;
            } else {
                $recharge->status = RechargeRecordTime::STATUS_FAULT;
            }
            $res = $recharge->save();
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['res'=>$res,'msg'=>"",'data'=>''];
    }

}
