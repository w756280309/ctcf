<?php

namespace api\modules\v1\controllers;

use common\models\user\User;

/**
 * 用户API.
 */
class LenderController extends Controller
{
    public function actionGet($id)
    {
        return $this->ensureLender($id);
    }

    public function actionUmp($id)
    {
        $lender = $this->ensureLender($id);
        $resp = \Yii::$container->get('ump')->getUserInfo($lender->epayUser->epayUserId);

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

    private function ensureLender($id)
    {
        $lender = User::findOne($id);
        if (null === $lender) {
            throw $this->ex404();
        }
        if (User::USER_TYPE_PERSONAL !== $lender->type) {
            throw $this->ex400('不是投资用户');
        }
        $epayUser = $lender->epayUser;
        if (null === $epayUser) {
            throw $this->ex400('无联动开户信息');
        }

        return $lender;
    }
}
