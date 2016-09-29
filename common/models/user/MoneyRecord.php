<?php

namespace common\models\user;

use common\utils\TxUtils;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "money_record".
 *
 * @property string $id
 * @property string $sn
 * @property int    $type
 * @property string $osn
 * @property string $account_id
 * @property string $uid
 * @property string $in_money
 * @property string $out_money
 * @property string $remark
 * @property string $create_at
 * @property string $updated_at
 */
class MoneyRecord extends \yii\db\ActiveRecord
{
    const TYPE_RECHARGE = 0; //充值
    const TYPE_RECHARGE_POS = 8; //线下充值
    const TYPE_DRAW = 1; //提现
    const TYPE_ORDER = 2; //投标
    const TYPE_FANGKUAN = 3; //放款
    const TYPE_HUANKUAN = 400; //还款
    const TYPE_HUIKUAN = 4; //回款
    const TYPE_CHEBIAO = 5; //流标
    const TYPE_CANCEL_ORDER = 51; //撤标
    const TYPE_FEE = 6; //放款扣去手续费
    const TYPE_FULL_TX = 7; //满标冻结金额转理财金额
    const TYPE_DRAW_CANCEL = 100; //提现撤销
    const TYPE_DRAW_SUCCESS = 101; //批量代付成功
    const TYPE_DRAW_RETURN = 102; //批量代付失败退款
    const TYPE_DRAW_FEE = 103; //提现手续费
    const TYPE_DRAW_FEE_RETURN = 104; //提现退回手续费
    const TYPE_CASH_GIFT = 105;//现金红包
    const TYPE_CREDIT_NOTE = 106;//购买转让
    const TYPE_CREDIT_NOTE_FEE = 107;//债权手续费
    const TYPE_CREDIT_REPAID = 108;//债权回款

    public static function createSN()
    {
        return TxUtils::generateSn('MR');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'money_record';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'account_id', 'uid'], 'integer'],
            [['account_id'], 'required'],
            [['in_money', 'out_money', 'balance'], 'number'],
            //[['out_money'],'number','min' =>0.01,'message' =>'提现金额必须大于0.01元人民币'],
            [['sn'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 500],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'type' => 'Type',
            'osn' => 'Osn',
            'account_id' => 'Account ID',
            'uid' => 'Uid',
            'in_money' => 'In Money',
            'out_money' => '提现金额',
            'balance' => '余额',
            'remark' => 'Remark',
            'created_at' => 'Create At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function getLenderMrType()
    {
        return [
            self::TYPE_RECHARGE,
            self::TYPE_DRAW,
            self::TYPE_DRAW_FEE,
            self::TYPE_DRAW_CANCEL,
            self::TYPE_DRAW_FEE_RETURN,
            self::TYPE_HUIKUAN,
            self::TYPE_DRAW_SUCCESS,
            self::TYPE_ORDER,
            self::TYPE_RECHARGE_POS,
            self::TYPE_CASH_GIFT,
            self::TYPE_CREDIT_NOTE,
            self::TYPE_CREDIT_NOTE_FEE,
            self::TYPE_CREDIT_REPAID,
        ];
    }
}
