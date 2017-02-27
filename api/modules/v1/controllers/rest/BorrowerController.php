<?php

namespace api\modules\v1\controllers\rest;

use api\modules\v1\controllers\Controller;
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

    public function actionUmp($userId = null, $umpId = null)
    {
        if (empty($umpId) && !empty($userId)) {
            $borrower = $this->ensureBorrower($userId);
            $epayUserId = $borrower->epayUser->epayUserId;
        } elseif(empty($umpId) && empty($id)) {
            return [];
        } else {
            $epayUserId = $umpId;
        }

        $resp = \Yii::$container->get('ump')->getMerchantInfo($epayUserId);

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
