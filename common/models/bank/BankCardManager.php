<?php

namespace common\models\bank;

use common\models\user\UserBank;
use yii\base\Exception;

class BankCardManager
{

    /**
     * 根据换卡申请记录，更换用户的卡信息
     * @param BankCardUpdate $bankCardUpdate
     * @return UserBank
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function confirmUpdate(BankCardUpdate $bankCardUpdate)
    {
        $oldSn = $bankCardUpdate->oldSn;
        $userBank = UserBank::find()->where(['binding_sn' => $oldSn])->one();
        //没有找到旧卡
        if (null === $userBank) {
            throw new Exception('old UserBank not fount');
        }
        //换卡记录状态不是1
        if (1 !== intval($bankCardUpdate->status)) {
            throw new Exception('the status of BankCardUpdate is not 1');
        }

        // 根据换卡记录，找到旧卡，删除旧卡，添加新卡
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $userBank->delete();
            $bank = new UserBank([
                'binding_sn' => $bankCardUpdate->sn,
                'uid' => $bankCardUpdate->uid,
                'epayUserId' => $bankCardUpdate->epayUserId,
                'bank_id' => $bankCardUpdate->bankId,
                'bank_name' => $bankCardUpdate->bankName,
                'account' => $bankCardUpdate->cardHolder,
                'card_number' => $bankCardUpdate->cardNo,
                'account_type' => '11',
            ]);
            $res = $bank->save(false);
            if (!$res) {
                throw new \yii\db\Exception('new UserBank save failed');
            }
            $transaction->commit();
            return $bank;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

}