<?php

namespace api\modules\v1\controllers;

use common\models\user\UserAccount as Account;

/**
 * 充值交易API.
 */
class AccountController extends Controller
{
    public function actionList()
    {
        $query = Account::find();

        $userId = $this->getQueryParamAsInt('user_id');
        if (null !== $userId) {
            $query->where(['uid' => $userId]);
        }

        $userMobile = $this->getQueryParam('user_mobile');
        if (null !== $userMobile && is_string($userMobile)) {
            if (!preg_match('/^1[34578]\d{9}$/', $userMobile)) {
                throw $this->exBadParam('user_mobile');
            }

            $query->select('user_account.*')
                ->leftJoin('user', 'user_account.uid = user.id')
                ->where('user.mobile = :mobile')
                ->addParams(['mobile' => $userMobile]);
        }

        return $this->paginate($query);
    }

    public function actionGet($id)
    {
        $loan = Account::findOne($id);
        if (null === $loan) {
            throw $this->ex404();
        }

        return $loan;
    }
}
