<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

class TradeLog extends \yii\db\ActiveRecord
{

    public function __construct(
        $user,
        $rq,
        $rp
    ) {
        parent::__construct();

        $this->tx_code = $rq->getTxCode();
        $this->tx_sn = $rq->getTxSn();
        $this->pay_id = 0;//默认
        $this->uid = $user->id;
        $this->account_id = $user->lendAccount->id;
        $this->request = $rq->getXml();
        if (null !== $rp) {
            $this->response_code = $rp->getCode();
            $this->response = $rp->getText();
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'trade_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
        ];
    }

    public function behaviors() {
        return [
             TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tx_code' => 'tx_code',
            'tx_sn' => 'tx_sn',
            'pay_id' => 'pay_id',
            'type' => 'Type',
            'uid' => 'Uid',
            'account_id' => 'Account ID',
            'request' => 'request',
            'response_code' => 'Response Code',
            'response' => 'Response',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
