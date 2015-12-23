<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\models\user\User;

/**
 * This is the model class for table "draw_record" 提现记录表.
 *
 */
class DrawRecord extends \yii\db\ActiveRecord {
    
    public $drawpwd ;
    private $_user = false;
    
    /* 提现状态 */

    const STATUS_ZERO = 0; //未处理
    const STATUS_EXAMINED = 1; //已审核
    const STATUS_SUCCESS = 2; //提现成功
    const STATUS_DENY = 11; //提现驳回

    public static function createSN($pre = '') {
        $pre_val = 'WD';
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999), $usec);
        return $pre_val . str_replace('x', $sec, $date);
    }

    public static function getStatus($key = null) {
        $data = [
            self::STATUS_ZERO => "未处理",
            self::STATUS_EXAMINED => "已审核",
            self::STATUS_SUCCESS => "提现成功",
            self::STATUS_DENY => "驳回",
        ];
        if (!empty($key)) {
            return $data[$key];
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'draw_record';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
//            ['drawpwd', 'trim'],
            [[ 'money','uid'], 'required'],//, 'bank_username', 'bank_account'      'account_id',,'drawpwd'
//            [['bank_id'], 'required', 'message' => '未选择提现银行卡'],
//            ['drawpwd', 'validatePassword'], wyf 注释的，因为写录入数据逻辑，该字段以后在考虑
            [['money'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9]{1,2})?$/', 'message' => '提现金额格式错误'],
            [['account_id', 'uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money'], 'number', 'min' => 1, 'max' => 10000000],
            [['sn', 'bank_id', 'bank_username', 'bank_account'], 'string', 'max' => 30]
        ];
    }
    
   /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateTradePwd($this->drawpwd,$user->trade_pwd)) {
                $this->addError($attribute, '密码错误.');
            }
        }
    }
    
    /**
     * Finds user by [[username]]
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
     * @inheritdoc
     */
    public function attributeLabels() {
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
            'drawpwd'=>'交易密码',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }
    
//    钩子函数怎么使用？？？
//    public function afterSave($insert)
//    {
//        if (parent::afterSave($insert)) {
//            if($insert) {
//                $this->user_id = Yii::$app->user->id;
//            }
//            return true;
//        } else {
//            return false;
//        }
//    }

}
