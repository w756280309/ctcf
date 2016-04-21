<?php

namespace common\models\user;

use yii\behaviors\TimestampBehavior;
use common\models\order\OnlineRepaymentRecord;
use common\lib\bchelp\BcRound;

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
    use \Zii\Model\ErrorExTrait;

    const TYPE_LEND = 1; //投资者
    const TYPE_BORROW = 2; //融资者

    /**
     * 属性列表
     */
    public function attributes()
    {
        return [
            'id',
            'type',
            'uid',
            'account_balance',
            'available_balance',
            'freeze_balance',
            'profit_balance',
            'investment_balance',
            'drawable_balance',
            'in_sum',
            'out_sum',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * 是投资账户？
     *
     * @return bool
     */
    public function isLender()
    {
        return self::TYPE_LEND === $this->type;
    }

    /**
     * 是融资账户？
     *
     * @return bool
     */
    public function isBorrower()
    {
        return self::TYPE_BORROW === $this->type;
    }

    /**
     * 获取关联的用户.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

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
            [['drawable_balance', 'investment_balance'], 'default', 'value' => 0],
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
            'investment_balance' => '理财金额',
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

    /**
     * 计算累计收益
     */
    public function getTotalProfit()
    {
        $total = OnlineRepaymentRecord::find()
            ->where(['status' => [OnlineRepaymentRecord::STATUS_DID, OnlineRepaymentRecord::STATUS_BEFORE], 'uid' => $this->uid])
            ->sum('lixi');

        return empty($total) ? '0.00' : $total;
    }

    /**
     * 资产总额 = 账户余额 + 理财资产
     */
    public function getTotalFund()
    {
        bcscale(14);
        $total = bcadd(bcadd($this->available_balance, $this->freeze_balance), $this->investment_balance);
        $bcRound = new BcRound();
        return $bcRound->bcround($total, 2);
    }
}
