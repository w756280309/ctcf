<?php

namespace common\models\payment;

use common\models\user\User;
use P2pl\PaymentTxInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Description of PaymentLog.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class PaymentLog extends ActiveRecord implements PaymentTxInterface
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['txSn', 'amount', 'toParty_id', 'loan_id'], 'required'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'toParty_id']);
    }

    public function getTxSn()
    {
        return $this->txSn;
    }

    public function getTxDate()
    {
        return $this->createdAt;
    }

    public function getEpayUserId()
    {
        return $this->user->epayUser->epayUserId;
    }

    public function getAmount()
    {
        return $this->amount;
    }
}
