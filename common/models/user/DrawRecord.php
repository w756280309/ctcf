<?php

namespace common\models\user;

use common\lib\bchelp\BcRound;
use common\models\draw\DrawException;
use common\utils\TxUtils;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "draw_record" 提现记录表.
 */
class DrawRecord extends \yii\db\ActiveRecord implements \P2pl\WithdrawalInterface
{
    public $drawpwd;
    private $_user = false;

    /* 提现状态 */
    const STATUS_ZERO = 0; //待受理
    const STATUS_EXAMINED = 1; //已受理
    const STATUS_SUCCESS = 2; //提现成功
    const STATUS_FAIL = 3; //提现失败
    const STATUS_DENY = 11; //提现驳回

    public static function createSN()
    {
        return TxUtils::generateSn('DR');
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'draw_record';
    }

    public static function getStatus($key = null)
    {
        $data = [
            self::STATUS_ZERO => '未处理',
            self::STATUS_EXAMINED => '已审核',
            self::STATUS_SUCCESS => '提现成功',
            self::STATUS_DENY => '驳回',
        ];
        if (!empty($key)) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['money', 'uid'], 'required'],
            [['money'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9]{1,2})?$/', 'message' => '提现金额格式错误'],
            [['account_id', 'uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number', 'max' => \Yii::$app->params['ump']['draw']['max']],
            [['fee'], 'number'],
            [['sn', 'bank_id', 'bank_name', 'bank_account'], 'string', 'max' => 30],
        ];
    }

    /**
     * 计算可以提现的金额.
     *
     * @param UserAccount $account
     * @param type        $money
     * @param type        $fee
     *
     * @return type
     *
     * @throws \Exception
     */
    public static function getDrawableMoney(UserAccount $account, $money, $fee = 0)
    {
        $bc = new BcRound();
        bcscale(14);
        $diff = bcsub($account->available_balance, $money);
        $max_draw = bcsub($account->available_balance, $fee);
        if (bccomp($diff, 0) < 0) {
            //余额不足
            throw new \Exception('可提现金额不足');
        } elseif (bccomp($diff, $fee) < 0) { //不够手续费
            throw new \Exception('可提现金额不足,提现金额不足以支付手续费');
        } elseif (0 === bccomp($max_draw, 0)) { //如果以上条件满足，提现金额与账户余额相等时候，取最大提现金额
            throw new DrawException($bc->bcround(bcsub($account->available_balance, $fee), 2), DrawException::ERROR_CODE_ENOUGH);
        }

        return $money;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateTradePwd($this->drawpwd, $user->trade_pwd)) {
                $this->addError($attribute, '密码错误.');
            }
        }
    }

    /**
     * Finds user by [[username]].
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne($this->uid);
        }

        return $this->_user;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => '对应资金账户id',
            'sn' => '流水号',
            'uid' => 'Uid',
            'money' => '提现金额',
            'bank_id' => '银行代号',
            'bank_name' => '银行账户',
            'bank_account' => '银行账号',
            'status' => '状态',
            'drawpwd' => '交易密码',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function getTxSn()
    {
        return $this->sn;
    }

    public function getTxDate()
    {
        return $this->created_at;
    }

    public function getEpayUserId()
    {
        return $this->user->epayUser->epayUserId;
    }

    public function getAmount()
    {
        return $this->money;
    }
}
