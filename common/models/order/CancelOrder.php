<?php

namespace common\models\order;

use yii\behaviors\TimestampBehavior;
use common\utils\TxUtils;

/**
 * 标的撤销交易表.
 */
class CancelOrder extends \yii\db\ActiveRecord implements \P2pl\OrderTxInterface
{
    const ORDER_CANCEL_INIT = 0;//初始
    const ORDER_CANCEL_ACK = 1;//处理中
    const ORDER_CANCEL_SUCCESS = 2;//成功
    const ORDER_CANCEL_FAIL = 3;//失败

    public static function tableName()
    {
        return 'cancelorder';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['orderSn', 'txSn', 'txStatus'], 'required'],
            [['txStatus'], 'integer'],
            [['money'], 'number'],
            [['orderSn', 'txSn'], 'string', 'max' => 30],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderSn' => '原订单编号',
            'txSn' => '交易号',
            'money' => '退款金额',
            'txStatus' => '状态',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 初始化撤销订单数据.
     *
     * @param OnlineOrder $order
     * @param type        $money 撤销金额 不填代表根据订单金额退款
     *
     * @return \self
     *
     * @throws \Exception
     */
    public static function initForOrder(OnlineOrder $order, $money = null)
    {
        $cancelOrder = new self([
            'orderSn' => $order->sn,
            'txSn' => TxUtils::generateSn(),
            'money' => (null === $money) ? $order->order_money : $money,
            'txStatus' => self::ORDER_CANCEL_ACK,
        ]);

        return $cancelOrder;
    }

    /**
     * 对应订单.
     *
     * @return UserBanks
     */
    public function getOrder()
    {
        return $this->hasOne(OnlineOrder::className(), ['sn' => 'orderSn']);
    }
    
    
    public function getLoanId()
    {
        return $this->order->online_pid;
    }

    public function getTxSn()
    {
        return $this->txSn;
    }

    public function getTxDate()
    {
        return $this->created_at;
    }

    public function getEpayUserId()
    {
        return $this->order->epayuser->epayUserId;
    }

    public function getAmount()
    {
        return $this->money;
    }

    public function getPaymentAmount()
    {
        return $this->order->paymentAmount;
    }
}
