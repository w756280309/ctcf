<?php

namespace app\modules\system\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\user\User;
use common\models\user\UserBanks;
use common\models\city\Region;

class SystemController extends BaseController {
   //系统设置页面
    public function actionSetting() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        $uid = $this->uid;
        
        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->select('usercode,mobile')->one();
        
        return $this->render('setting', ['model' => $user]);
    }
    
    public function actionSafecenter() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        $uid = $this->uid;
        
        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE, 'idcard_status' => User::IDCARD_STATUS_PASS])->select('idcard')->one(); 
        $user_bank = UserBanks::find()->where(['uid' => $uid, 'status' => UserBanks::STATUS_YES])->select('bank_name,card_number')->one();
        
        return $this->render('safecenter', ['user' => $user, 'user_bank' => $user_bank]);
    }
    
    public function actionHelp()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('help');
    }
    
    public function actionProblem()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('problem');
    }
    
    public function actionAbout()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('about');
    }
    
    public function actionCity($pid=null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $city = Region::find()->where(['province_id'=>$pid])->select('name')->asArray()->all();
        
        return ['code' => 0, 'city' => $city, 'message' => '成功'];
    }
}
