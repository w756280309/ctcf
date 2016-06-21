<?php

namespace common\models\user;

use common\utils\TxUtils;
use yii\behaviors\TimestampBehavior;
use common\lib\bchelp\BcRound;
use common\models\draw\DrawException;
use common\models\user\UserAccount ;

/**
 * This is the model class for table "draw_record" 提现记录表.
 */
class DrawRecord extends \yii\db\ActiveRecord implements \P2pl\WithdrawalInterface
{
    public $drawpwd;
    private $_user = false;

    /* 提现状态 */
    const STATUS_ZERO = 0; //未处理
    const STATUS_EXAMINED = 1; //已审核
    const STATUS_SUCCESS = 2; //提现成功
    const STATUS_FAIL = 3; //提现不成功
    const STATUS_LAUNCH_BATCHPAY = 4; //已放款,此时生成批量代付批次
    const STATUS_DEAL_FINISH = 5; //已经处理
    const STATUS_DENY = 11; //提现驳回
//
//    /**
//     * 发起提现，TODO：去掉user和ubank
//     */
//    public static function initForAccount($user, $money)
//    {
//        $ubank = $user->qpay;
//        $account = $user->lendAccount;
//        $money = self::getRealDrawFound($account, $money); //计算用户实际提现金额以及写入扣除手续费记录
//        $draw = new self();
//        $draw->sn = self::createSN();
//        $draw->money = $money;
//        $draw->pay_id = 0; // 支付公司ID
//        $draw->account_id = $account->id;
//        $draw->uid = $user->id;
//        $draw->pay_bank_id = '0'; // TODO
//        $draw->bank_id = $ubank->bank_id;
//        $draw->bank_name = $ubank->bank_name;
//        $draw->bank_account = $ubank->card_number;
//        $draw->identification_type= $ubank->account_type;
//        $draw->identification_number = $user->idcard;
//        $draw->user_bank_id = $ubank->id;
//        $draw->sub_bank_name = $ubank->sub_bank_name;
//        $draw->province = $ubank->province;
//        $draw->city = $ubank->city;
//        $draw->mobile = $user->mobile;
//        $draw->status = DrawRecord::STATUS_ZERO;
//        return $draw;
//    }

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
        if (bccomp($diff, 0) < 0) {//余额不足
            throw new \Exception('可提现金额不足');
        } else if (bccomp($diff, $fee) <= 0 && 0 !== bccomp($diff, 0)) { //不够手续费
            throw new \Exception('可提现金额不足,提现金额不足以支付手续费');
        } else if (0 === bccomp($max_draw, 0)) { //如果以上条件满足，提现金额与账户余额相等时候，取最大提现金额
            throw new DrawException($bc->bcround(bcsub($account->available_balance, $fee), 2), DrawException::ERROR_CODE_ENOUGH);
        }
        return $money;
//        exit;
//        $drawableMoney = self::getDrawableMoney($account, $fee);
//        if (
//            false === $drawableMoney
//            || 0 > bccomp($drawableMoney, $money)
//         ) {
//            if (0 === bccomp($account->available_balance, $money)) {
//                if (bccomp($drawableMoney, \Yii::$app->params['ump']['draw']['min']) >= 0) {
//                    throw new DrawException($drawableMoney, DrawException::ERROR_CODE_ENOUGH);
//                } else {
//                    throw new \Exception('可提现金额不足,提现金额不足以支付手续费');
//                }
//            } else {
//                throw new \Exception('可提现金额不足');
//            }
//        }
//
//        return $money;
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

    /**
     * 计算用户实际提现金额以及生成冻结手续费记录.
     *
     * @param UserAccount $ua    用户资金对象
     * @param decimal     $money 提现金额
     * @param varchar     $osn   提现编号
     *                           提现金额+手续费>=可用余额 提现金额为实际到账金额
     *                           提现金额+手续费<可用余额 提现金额-手续费为实际到账金额
     */
    public static function getRealDrawFound($ua, $money)
    {
        bcscale(14);
        $bc = new BcRound();
        if (0 > bccomp($ua->available_balance, bcadd($money, \Yii::$app->params['drawFee']))) {
            $money = $bc->bcround(bcsub($money, \Yii::$app->params['drawFee']), 2);
        }

        return $money;
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
