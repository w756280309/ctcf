<?php

namespace app\modules\system\controllers;

use app\controllers\BaseController;
use common\models\user\User;
use common\models\user\QpayBinding;
use common\service\BankService;

class SystemController extends BaseController
{
    public $layout = '@app/modules/order/views/layouts/buy';

    /**
     * 系统设置页面
     */
    public function actionSetting()
    {
        $uid = $this->user->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->select('usercode,mobile')->one();

        return $this->render('setting', ['model' => $user]);
    }

    /**
     * 账户中心
     */
    public function actionSafecenter()
    {
        $uid = $this->user->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE, 'idcard_status' => User::IDCARD_STATUS_PASS])->select('idcard')->one();
        $user_bank = null;
        $qpaystatus = BankService::getQpayStatus($this->user);
        if (User::QPAY_ENABLED === $qpaystatus) {
            $user_bank = $this->user->qpay;
        } else if (User::QPAY_PENDING === $qpaystatus) {
            $user_bank = QpayBinding::findOne(['uid' => $uid, 'status' => QpayBinding::STATUS_ACK]);
        }
        return $this->render('safecenter', ['user' => $user, 'user_bank' => $user_bank]);
    }
}
