<?php

namespace common\models\bank;


use common\models\user\UserBank;

class BankCardManager
{
    public function confirmUpdate(BankCardUpdate $bankCardUpdate)
    {
        $oldSn = $bankCardUpdate->oldSn;
        $userBank = UserBank::find()->where(['binding_sn' => $oldSn])->one();
        //todo  根据换卡记录，找到旧卡，删除旧卡，添加新卡
        if ($userBank) {
            $userBank->delete();
            $bank = new UserBank([
                'binding_sn' => '',
                'uid' => '',
                'epayUserId' => '',
                'bank_id' => '',
                'bank_name' => '',
                'sub_bank_name' => '',
                'province' => '',
                'city' => '',
                'account' => '',
                'card_number' => '',
                'account_type' => '',
                'mobile' => '',
                'status' => '',
            ]);
            $bank->save();
            return $bank;
        }
        return null;
    }

}