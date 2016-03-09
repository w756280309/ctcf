<?php

namespace api\modules\v1\controllers;

use common\models\user\User;

/**
 * 充值交易API.
 */
class UserController extends Controller
{
    public function actionGet($id)
    {
        $user = User::findOne($id);
        if (null === $user) {
            throw $this->ex404();
        }

        return $user;
    }

    public function actionUmp($id)
    {
        $user = $this->actionGet($id);
        $resp = \Yii::$container->get('ump')->getUserInfo($user->epayUser->epayUserId);

        return [
            'mer_id' => $resp->get('mer_id'),
            'account_id' => $resp->get('account_id'),
            'account_state' => $resp->get('account_state'),
            'card_id' => $resp->get('card_id'),
            'contact_mobile' => $resp->get('contact_mobile'),
            'cust_name' => $resp->get('cust_name'),
            'gate_id' => $resp->get('gate_id'),
            'plat_user_id' => $resp->get('plat_user_id'),
            'user_bind_agreement_list' => $resp->get('user_bind_agreement_list'),
            'balance' => $resp->get('balance'),
        ];
    }
}
