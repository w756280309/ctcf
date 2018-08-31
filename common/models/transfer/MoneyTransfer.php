<?php

namespace common\models\transfer;

use common\models\product\OnlineProduct;
use common\models\user\User;
use common\models\user\UserBank;
use common\utils\TxUtils;
use Zii\Behavior\DateTimeBehavior;
use yii\db\ActiveRecord;

class MoneyTransfer extends ActiveRecord
{
    const TYPE_BORROWER = 'borrower';
    const TYPE_PLATFORM = 'platform';
    const TYPE_LOAN = 'loan';
    const TYPE_BANK = 'bank';

    public function behaviors()
    {
        return [
            DateTimeBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            [['fromId', 'toId', 'fromType', 'toType', 'amount'], 'required'],
            [['retMsg', 'remark'], 'string', 'max' => 255],
            ['amount', 'double'],
            ['status', 'string'],
        ];
    }

    /**
     * 判断是否为商户间转账
     *
     * @return bool
     */
    public function isMerToMer()
    {
        return in_array($this->fromType, [self::TYPE_BORROWER, self::TYPE_PLATFORM])
            && in_array($this->toType, [self::TYPE_BORROWER, self::TYPE_PLATFORM]);
    }

    /**
     * 获得转账方或收款方主体
     *
     * @param string $whichFang 转账方from、收款方to
     *
     * @return null|OnlineProduct|User|PlatformTx|UserBank
     */
    public function getTransferObject($whichFang)
    {
        $obj = null;
        if (!in_array($whichFang, ['from', 'to'])) {
            return $obj;
        }

        $attributeType = $this->{$whichFang . 'Type'};
        $attribute = $this->{$whichFang . 'Id'};
        if ($attributeType === self::TYPE_LOAN) {
            $obj = OnlineProduct::findOne($attribute);
        } elseif ($attributeType === self::TYPE_BORROWER) {
            $obj = User::findOne([
                'id' => $attribute,
                'type' => User::USER_TYPE_ORG,
            ]);
        } elseif ($attributeType === self::TYPE_PLATFORM) {
            $obj = new PlatformTx(['id' => $attribute]);
        } elseif ($attributeType === self::TYPE_BANK) {
            $obj= UserBank::findOne($attribute);
        }

        return $obj;
    }

    /**
     * 初始化MoneyTransfer
     *
     * @param string $fromId 付款方ID
     * @param string $fromType 付款方类型
     * @param string $toId 收款方ID
     * @param string $toType 收款方类型
     * @param string $amount 转账金额
     *
     * @return MoneyTransfer
     */
    public static function initNew($fromId, $fromType, $toId, $toType, $amount)
    {
        return new MoneyTransfer([
            'sn' => TxUtils::generateSn('TR'),
            'fromId' => $fromId,
            'fromType' => $fromType,
            'toId' => $toId,
            'toType' => $toType,
            'amount' => $amount,
            'status' => 'init',
        ]);
    }
}