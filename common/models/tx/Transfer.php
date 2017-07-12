<?php

namespace common\models\tx;

use Yii;
use Zii\Behavior\DateTimeBehavior;
use Zii\Model\ActiveRecord;

/**
 * 转账表.
 *
 * @property $id           integer 主键
 * @property $sn           string  流水            前缀TSFR
 * @property $type         string  交易类型         buy_note：购买者付款;
 * @property $amount       string  金额            实际支付金额
 * @property $fromAccount  string  付款账户         存对应转出方ID
 * @property $toAccount    string  收款账户         存对应转入方ID
 * @property $sourceType   string  相关交易类型      存对应表名
 * @property $sourceTxSn   string  相关交易流水号    存对应ID
 * @property $status       string  转账状态         0:初始;1:成功;2:失败;3:未知
 */
class Transfer extends ActiveRecord
{
    const STATUS_INIT = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;
    const STATUS_OTHER = 3;

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

    public static function initNew($config = [])
    {
        $transfer = new self($config);
        $transfer->sn = FinUtils::generateSn('TSFR');
        $transfer->status = self::STATUS_INIT;

        return $transfer;
    }

    /**
     * 根据转让订单新建转账记录.
     *
     * @param CreditOrder $order       转让订单对象
     * @param int                    $amount      转账金额
     * @param int                    $fromAccount 转出账户ID
     * @param int                    $toAccount   转入账户ID
     * @param string                 $type        转账类型
     */
    public static function initByCreditOrder(CreditOrder $order, $amount, $fromAccount, $toAccount, $type = 'buy_note')
    {
        //新建transfer
        $transfer = self::initNew([
            'type' => $type,
            'amount' => $amount,
            'fromAccount' => $fromAccount,
            'toAccount' => $toAccount,
            'sourceType' => CreditOrder::tableName(),
            'sourceTxSn' => $order->id,
        ]);

        return $transfer;
    }
}
