<?php

namespace common\models\user;

use common\utils\TxUtils;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "money_record".
 *
 * @property string $id
 * @property string $sn
 * @property int $type
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
    const TYPE_DRAW = 1; //提现
    const TYPE_ORDER = 2; //投标
    const TYPE_FANGKUAN = 3; //放款
    const TYPE_HUANKUAN = 4; //还款
    const TYPE_CHEBIAO = 5; //撤标
    const TYPE_FEE = 6; //放款扣去手续费
    const TYPE_DRAW_RETURN = 7; //提现退款

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
            'create_at' => 'Create At',
            'updated_at' => 'Updated At',
        ];
    }
}
