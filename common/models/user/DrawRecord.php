<?php

namespace common\models\user;

use common\utils\TxUtils;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "draw_record" 提现记录表.
 */
class DrawRecord extends \yii\db\ActiveRecord
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

    /**
     * 发起提现，TODO：去掉user和ubank
     */
    public static function initForAccount($account, $money)
    {
        $user = $account->user;
        $ubank = $user->bank;

        $draw = new self();
        $draw->money = $money;
        $draw->sn = self::createSN();
        $draw->pay_id = 0; // 支付公司ID
        $draw->account_id = $account->id;
        $draw->uid = $user->id;
        $draw->pay_bank_id = '0'; // TODO
        $draw->bank_id = $ubank->bank_id;
        $draw->bank_username = $ubank->bank_name;
        $draw->bank_account = $ubank->card_number;
        $draw->identification_type= $ubank->account_type;
        $draw->identification_number = $user->idcard;
        $draw->user_bank_id = $ubank->id;
        $draw->sub_bank_name = $ubank->sub_bank_name;
        $draw->province = $ubank->province;
        $draw->city = $ubank->city;
        $draw->mobile = $user->mobile;
        $draw->status = DrawRecord::STATUS_ZERO;

        return $draw;
    }

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
//            ['drawpwd', 'trim'],
            [['money', 'uid'], 'required'], //, 'bank_username', 'bank_account'      'account_id',,'drawpwd'
//            [['bank_id'], 'required', 'message' => '未选择提现银行卡'],
//            ['drawpwd', 'validatePassword'], wyf 注释的，因为写录入数据逻辑，该字段以后在考虑
            [['money'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9]{1,2})?$/', 'message' => '提现金额格式错误'],
            [['account_id', 'uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number', 'min' => 1, 'max' => 10000000],
            [['sn', 'bank_id', 'bank_username', 'bank_account'], 'string', 'max' => 30],
        ];
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
            'bank_username' => '银行账户',
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
}
