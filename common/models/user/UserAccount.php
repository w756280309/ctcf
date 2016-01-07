<?php

namespace common\models\user;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_account".
 *
 * @property string $id
 * @property int $type
 * @property string $uid
 * @property string $account_balance
 * @property string $available_balance
 * @property string $drawable_balance
 * @property string $freeze_balance
 * @property string $in_sum
 * @property string $out_sum
 * @property string $create_at
 * @property string $updated_at
 */
class UserAccount extends \yii\db\ActiveRecord
{
    const TYPE_LEND = 1; //投资者
    const TYPE_BORROW = 2; //融资者

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'uid'], 'integer'],
            [['uid'], 'required'],
            [['account_balance', 'available_balance', 'freeze_balance', 'in_sum', 'out_sum'], 'number'],
            ['drawable_balance', 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '类型',
            'uid' => 'Uid',
            'account_balance' => '账户余额',
            'available_balance' => '可用余额',
            'freeze_balance' => '冻结余额',
            'drawable_balance' => '可提现余额',
            'in_sum' => '账户入金总额',
            'out_sum' => '账户出金总额',
            'created_at' => '创建时间',
            'updated_at' => '编辑时间',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
