<?php

namespace app\modules\system\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\user\User;
use common\models\user\QpayBinding;
use common\models\city\Region;

class SystemController extends BaseController
{
    public $layout = '@app/modules/order/views/layouts/buy';
    
    //系统设置页面
    public function actionSetting()
    {
        $uid = $this->user->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->select('usercode,mobile')->one();

        return $this->render('setting', ['model' => $user]);
    }

    public function actionSafecenter()
    {
        $uid = $this->user->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE, 'idcard_status' => User::IDCARD_STATUS_PASS])->select('idcard')->one();
        $user_bank = QpayBinding::find()->where(['uid' => $uid])->select('bank_name,card_number,status')->orderBy('id desc')->one();

        return $this->render('safecenter', ['user' => $user, 'user_bank' => $user_bank]);
    }

    public function actionHelp()
    {
        return $this->render('help');
    }

    public function actionProblem()
    {
        return $this->render('problem');
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionCity($pid = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $city = Region::find()->where(['province_id' => $pid])->select('name')->asArray()->all();

        return ['code' => 0, 'city' => $city, 'message' => '成功'];
    }
}
