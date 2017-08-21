<?php

namespace console\command;


use common\models\queue\Job;
use common\models\user\OpenAccount;
use common\models\user\User;

class OpenAccountJob extends Job
{
    public function run()
    {
        $id = $this->getParam('openAccountRecordId');
        $openAccount = OpenAccount::find()
            ->where([
                'id' => $id,
                'status' => OpenAccount::STATUS_INIT,
            ])->one();
        if (is_null($openAccount)) {
            throw new \Exception('没有找到开户记录');
        }
        /**
         * @var User $user
         * @var OpenAccount $openAccount
         */
        $user = $openAccount->user;
        try {
            $user->setIdentity($openAccount);
            $openAccount->status = OpenAccount::STATUS_SUCCESS;
            $openAccount->save();
        } catch (\Exception $e) {
            $openAccount->status = OpenAccount::STATUS_FAIL;
            $openAccount->save();
            throw new $e;
        }
    }
}