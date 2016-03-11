<?php

namespace app\modules\system\controllers;

use app\controllers\BaseController;
use common\models\user\User;
use common\models\user\QpayBinding;
use common\service\BankService;

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
        $user_bank = null;
        $qpaystatus = BankService::getQpayStatus($this->user);
        if (User::QPAY_ENABLED === $qpaystatus) {
            $user_bank = $this->user->qpay;
        } else if (User::QPAY_PENDING === $qpaystatus) {
            $user_bank = QpayBinding::findOne(['uid' => $uid, 'status' => QpayBinding::STATUS_ACK]);
        }
        return $this->render('safecenter', ['user' => $user, 'user_bank' => $user_bank]);
    }

    /**
     * 新手帮助
     * @return type
     */
    public function actionHelp($type=null)
    {
        switch ($type) {
            case 1: return $this->render('help_loginregister');
            case 2: return $this->render('help_bindcard');
            case 3: return $this->render('help_invest');
            default: return $this->render('help');
        }
    }

    /**
     * 常见问题
     * @return type
     */
    public function actionProblem()
    {
        return $this->render('problem');
    }

    /**
     * 关于我们
     * @return type
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * 资讯列表
     * @return type
     */
    public function actionMesslist()
    {
        return $this->render('messlist');
    }

    /**
     *
     */
    public function actionMessdetail()
    {
        return $this->render('messdetail');
    }
}
