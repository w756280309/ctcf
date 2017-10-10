<?php

namespace common\models\tx;

use common\models\epay\EpayUser;
use common\models\order\EbaoQuan;
use common\models\user\User;
use Zii\Behavior\DateTimeBehavior;
use Zii\Model\ActiveRecord;
use Yii;

/**
 * @property int    $id                 主键
 * @property int    $user_id            用户ID
 * @property int    $note_id            挂牌记录ID
 * @property int    $asset_id           用户资产ID
 * @property string $amount             支付金额
 * @property string $principal          本金
 * @property string $interest           应付利息
 * @property string $fee                手续费
 * @property int    $status             状态: 0初始, 1成功, 2失败, 3处理订单异常
 * @property string $createTime         创建时间
 * @property string $updateTime         更新时间
 * @property int    $buyerPaymentStatus 买方付款状态  状态：0未支付、1支付成功、2支付失败、3支付不明、4回滚支付成功、5回滚支付失败、6回滚支付异常
 * @property int    $sellerRefundStatus 卖方回款状态  状态：0未支付、1支付成功、2支付失败、3支付不明
 * @property int    $feeTransferStatus  手续费转账状态 状态：0未支付、1支付成功、2支付失败、3支付不明
 * @property string $buyerAmount          购买人持有当前对应标的金额，对应南金交产品转让信息转入人剩余份额字段，在定时任务中该笔订单成功后更新
 * @property string $sellerAmount         转让方持有当前对应标的金额，对应是对接南金交产品转让信息转出人剩余份额字段，在定时任务中该笔订单成功后更新
 * @property string $settleTime         记账时间，用来查询每日需要对接的给南金交产品的转让信息，在定时任务中该笔订单成功后更新
 */
class CreditOrder extends ActiveRecord
{
    const STATUS_INIT = 0;      //初始化
    const STATUS_SUCCESS = 1;   //成功
    const STATUS_FAIL = 2;      //失败
    const STATUS_OTHER = 3;     //处理订单异常

    public static function getDb()
    {
        return Yii::$app->db_tx;
    }

    public function behaviors()
    {
        return [
            'datetime' => [
                'class' => DateTimeBehavior::class,
            ],
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'note_id', 'asset_id', 'status', 'buyerPaymentStatus', 'sellerRefundStatus', 'feeTransferStatus'], 'integer'],
            [['amount', 'principal', 'interest', 'fee'], 'string'],
            [['user_id', 'note_id', 'asset_id', 'status', 'amount', 'principal', 'interest', 'fee'], 'required'],
        ];
    }

    /**
     * 自定义订单验证方法
     * 验证订单金额.
     *
     * @return bool
     */
    public function validateOrder()
    {
        if (!$this->validate()) {
            return false;
        }
        $note = CreditNote::findOne($this->note_id);
        if (null === $note || $note->isCancelled || $note->isClosed) {
            $this->addError('note_id', '没有找到指定债权');

            return false;
        }
        $asset = UserAsset::findOne($this->asset_id);
        if (null === $asset) {
            $this->addError('asset_id', '没有找到指定资产');

            return false;
        }
        $config = json_decode($note->config, true);
        $noteTradeableAmount = bcsub($note->amount, $note->tradedAmount, 0);//可交易金额
        try {
            FinUtils::canBuildCreditByAmount($noteTradeableAmount, $this->principal, $config['min_order_amount'], $config['incr_order_amount']);
        } catch (\Exception $ex) {
            $this->addError('principal', $ex->getMessage());
        }

        return $this->hasErrors() ? false : true;
    }

    /**
     * 从post数据直接初始化一个订单对象
     *
     * @return CreditOrder
     */
    public static function initNew()
    {
        $order = new self();
        $order->status = self::STATUS_INIT;
        $order->buyerPaymentStatus = 0;
        $order->sellerRefundStatus = 0;
        $order->feeTransferStatus = 0;

        return $order;
    }

    public function getAsset()
    {
        return $this->hasOne(UserAsset::className(), ['id' => 'asset_id']);
    }

    public function getFcUser()
    {
        return $this->hasOne(EpayUser::className(), ['appUserId' => 'user_id']);
    }

    public function getNote()
    {
        return $this->hasOne(CreditNote::className(), ['id' => 'note_id']);
    }

    /**
     * 获取用户资产对应的订单信息.
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function fetchBaoQuan()
    {
        return EbaoQuan::find()->where([
            'itemType' => EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
            'type' => EbaoQuan::TYPE_E_CREDIT,
            'success' => 1,
            'uid' => $this->user_id,
            'itemId' => $this->id,
        ])->one();
    }
}
