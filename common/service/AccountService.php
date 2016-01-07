<?php

namespace common\service;

use common\models\user\DrawRecord;
use common\models\user\UserAccount;

class AccountService
{
    public function initDraw(UserAccount $account, $money)
    {
        $draw = DrawRecord::initForAccount($account, $money);
    }
}
