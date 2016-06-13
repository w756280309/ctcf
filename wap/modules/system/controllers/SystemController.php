<?php

namespace app\modules\system\controllers;

use app\controllers\BaseController;
use common\models\user\User;
use common\models\user\QpayBinding;
use common\service\BankService;

class SystemController extends BaseController
{
    /**
     * 系统设置页面
     */
    public function actionSetting()
    {
        $uid = $this->getAuthedUser()->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->select('usercode,mobile')->one();

        return $this->render('setting', ['model' => $user]);
    }

    /**
     * 账户中心
     */
    public function actionSafecenter()
    {
        $user = $this->getAuthedUser();

        $user_bank = $user->qpay;

        if (null === $user_bank) {
            $user_bank = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);
        }

        return $this->render('safecenter', ['user' => $user, 'user_bank' => $user_bank]);
    }
}
