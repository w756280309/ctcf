<?php

namespace app\modules\system\controllers;

use app\controllers\BaseController;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;
use common\models\user\QpayBinding;
use Yii;

class SystemController extends BaseController
{
    /**
     * 系统设置页面
     */
    public function actionSetting()
    {
        $uid = $this->getAuthedUser()->id;

        $user = User::find()->where(['id' => $uid, 'type' => User::USER_TYPE_PERSONAL, 'status' => User::STATUS_ACTIVE])->select('usercode, safeMobile')->one();

        return $this->render('setting', ['model' => $user]);
    }

    /**
     * 安全中心
     */
    public function actionSafecenter()
    {
        $user = $this->getAuthedUser();

        $user_bank = $user->qpay;

        if (null === $user_bank) {
            $user_bank = QpayBinding::findOne(['uid' => $user->id, 'status' => QpayBinding::STATUS_ACK]);
        }

        $closeWin = false;

        if ($this->fromWx() && Yii::$app->session->has('resourceOwnerId')) {
            $openId = Yii::$app->session->get('resourceOwnerId');

            if ((new SocialConnect())->isConnected($user, $openId, SocialConnect::PROVIDER_TYPE_WECHAT)) {
                $closeWin = true;
            }
        }

        return $this->render('safecenter', [
            'user' => $user,
            'user_bank' => $user_bank,
            'closeWin' => $closeWin,
        ]);
    }
}
