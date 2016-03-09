<?php

namespace api\modules\v1\controllers;

use common\models\epay\EpayUser;
use common\models\user\User;

class BorrowerController extends Controller
{
    public function actionList()
    {
        $query = User::find()
            ->select('user.*')
            ->innerJoin('EpayUser', 'EpayUser.appUserId = user.id')
            ->where('user.type = :userType')
            ->addParams([
                'userType' => User::USER_TYPE_ORG,
            ]);

        return $this->paginate($query);
    }

    public function actionGet($id)
    {
        return $this->ensureBorrower($id);
    }

    public function actionUmp($id)
    {
        $borrower = $this->ensureBorrower($id);

        $resp = \Yii::$container->get('ump')->getMerchantInfo($borrower->epayUser->epayUserId);

        return [
            'mer_id' => $resp->get('query_mer_id'),
            'account_state' => $resp->get('account_state'),
            'account_type' => $resp->get('account_type'),
            'balance' => $resp->get('balance'),
        ];
    }

    private function ensureBorrower($id)
    {
        $borrower = User::findOne($id);
        if (null === $borrower) {
            throw $this->ex404();
        }
        if (User::USER_TYPE_ORG !== $borrower->type) {
            throw $this->ex400('不是融资用户');
        }
        $epayUser = $borrower->epayUser;
        if (null === $epayUser) {
            throw $this->ex400('无联动开户信息');
        }

        return $borrower;
    }
}
