<?php

namespace common\models\product;

use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "credit_trade".
 *
 * @property integer $id
 * @property integer $loan_id
 * @property integer $order_id
 * @property integer $user_id
 * @property string $amount
 * @property string $tradedAmount
 * @property string $discountRate
 * @property integer $status
 * @property integer $holdDays
 * @property string $maxDiscountRate
 * @property integer $tradeCountLimit
 * @property string $feeRate
 * @property string $minOrderAmount
 * @property string $incrOrderAmount
 * @property integer $isTest
 * @property string $createTime
 * @property string $endTime
 * @property string $closeTime
 * @property string $cancelTime
 */
class CreditTrade extends ActiveRecord
{
    const STATUS_ONGOING = 1;   //转让中
    const STATUS_END = 2;   //结束
    const STATUS_CANCEL = 3;    //撤销

    public function __construct($config = [])
    {
        $this->tradedAmount = 0;
        $this->discountRate = 0;
        $this->holdDays = Yii::$app->params['credit_trade']['hold_days'];
        $this->maxDiscountRate = Yii::$app->params['credit_trade']['max_discount_rate'];
        $this->tradeCountLimit = Yii::$app->params['credit_trade']['trade_count_limit'];
        $this->feeRate = Yii::$app->params['credit_trade']['fee_rate'];
        $this->minOrderAmount = Yii::$app->params['credit_trade']['min_order_amount'];
        $this->incrOrderAmount = Yii::$app->params['credit_trade']['incr_order_amount'];
        $this->isTest = 0;
        $this->createTime = date('Y-m-d H:i:s');

        $listingDuration = Yii::$app->params['credit_trade']['listing_duration'];
        $this->endTime = date("Y-m-d H:i:s", strtotime("$this->createTime +$listingDuration days"));    //计算到期时间

        if (!empty($config)) {
            Yii::configure($this, $config);
        }
        $this->init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['loan_id', 'order_id', 'user_id', 'amount', 'tradedAmount', 'discountRate', 'status', 'holdDays', 'maxDiscountRate', 'tradeCountLimit', 'feeRate', 'minOrderAmount', 'incrOrderAmount', 'isTest', 'createTime', 'endTime'], 'required'],
            [['loan_id', 'order_id', 'user_id', 'status', 'holdDays', 'tradeCountLimit', 'isTest'], 'integer'],
            [['amount', 'tradedAmount', 'discountRate', 'maxDiscountRate', 'feeRate', 'minOrderAmount', 'incrOrderAmount'], 'number'],
            [['createTime', 'endTime', 'closeTime', 'cancelTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'loan_id' => '标的ID',
            'order_id' => '订单ID',
            'user_id' => '转让人ID',
            'amount' => '转让金额',
            'tradedAmount' => '实际转让金额',
            'discountRate' => '折让率', //单位为%
            'status' => '状态',
            'holdDays' => '持有天数',
            'maxDiscountRate' => '最大折让率',  //单位为%
            'tradeCountLimit' => '可转让次数',
            'feeRate' => '手续费费率',  //例如千分之三，就存0.003
            'minOrderAmount' => '起投金额',
            'incrOrderAmount' => '递增金额',
            'isTest' => '是否是测试记录',
            'createTime' => '创建时间',
            'endTime' => '到期时间',
            'closeTime' => '关闭时间',
            'cancelTime' => '撤销时间',
        ];
    }

    /**
     * 获取相关标的信息.
     */
    public function getLoan()
    {
        return $this->hasOne(OnlineProduct::className(), ['id' => 'loan_id']);
    }

    /**
     * 获取相关标的信息.
     */
    public function getOrder()
    {
        return $this->hasOne(OnlineOrder::className(), ['id' => 'order_id']);
    }
}
