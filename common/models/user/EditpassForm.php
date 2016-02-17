<?php

namespace common\models\user;

use Yii;
use yii\base\Model;

/**
 * Editpass form.
 */
class EditpassForm extends Model
{
    public $password;
    public $new_pass;
    public $r_pass;
    public $verifyCode;
    private $_id = false;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            'add' => ['new_pass', 'r_pass'], //新增交易密码
            'edit' => ['password', 'new_pass', 'verifyCode'], //修改交易密码
            'edituserpass' => ['password', 'new_pass', 'verifyCode'], //修改登陆密码
            'checktradepwd' => ['password'], //检查交易密码
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['password', 'required', 'message' => '交易密码不能为空', 'on' => ['edit', 'checktradepwd']],
            ['password', 'required', 'message' => '原登录密码不能为空', 'on' => 'edituserpass'],
            ['new_pass', 'required', 'message' => '新密码不能为空', 'on' => ['add', 'edit', 'edituserpass']],
            ['r_pass', 'required', 'message' => '新密码不能为空', 'on' => ['add']],
            ['new_pass', 'integer', 'message' => '交易密码必须为整数', 'on' => ['add']],
            ['r_pass', 'integer', 'message' => '确认密码必须为整数', 'on' => ['add']],
            [['password', 'new_pass', 'r_pass'], 'integer', 'on' => ['add', 'edit', 'checktradepwd']],
            ['password', 'string', 'length' => 6, 'on' => ['edit', 'checktradepwd']],
            ['new_pass', 'string', 'length' => 6, 'on' => ['add', 'edit']],
            ['r_pass', 'string', 'length' => 6, 'on' => ['add']],
            ['r_pass', 'compare', 'compareAttribute' => 'new_pass', 'message' => '两次输入的密码不一致', 'on' => ['add']],
            ['password', 'validateTradePwd', 'on' => ['edit', 'checktradepwd']],
            [['password', 'new_pass', 'r_pass'], 'match', 'pattern' => '/^[0-9]{6,6}$/', 'message' => '交易密码必须为6位纯数字', 'on' => ['add', 'edit', 'checktradepwd']],
            [['password', 'new_pass'], 'string', 'length' => [6, 20], 'on' => 'edituserpass'],
            ['password', 'validatePassword', 'on' => 'edituserpass'],
            ['new_pass', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/', 'message' => '新密码必须为数字和字母的组合', 'on' => 'edituserpass'],
            ['verifyCode', 'captcha', 'on' => ['edit', 'edituserpass']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'password' => '用户密码',
            'new_pass' => '新密码',
            'r_pass' => '确认密码',
            'verifyCode' => '',
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
            $id = $this->getId();
            if (!$id || !$id->validatePassword($this->password)) {
                $this->addError($attribute, '原始登陆密码不正确!');
            }
        }
    }

    /**
     * Validates the trade_pwd.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validateTradePwd($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $id = $this->getId();
            if (!$id || !$id->validateTradePwd($this->password, $id->trade_pwd)) {
                $this->addError($attribute, '交易密码不正确!');
            }
        }
    }

    /**
     * 修改交易密码
     *
     * @return bool whether the user is logged in successfully
     */
    public function editpass()
    {
        $model = $this->getId();
        if ($this->validate()) {
            $model->scenario = 'editpass';
            $model->trade_pwd = $model->setTradePassword($this->new_pass);

            return $model->save();
        }

        return false;
    }

    /**
     * 修改用户密码
     *
     * @return bool whether the user is logged in successfully
     */
    public function edituserpass()
    {
        $model = $this->getId();
        if ($this->validate()) {
            $model->scenario = 'editpass';
            $model->setPassword($this->new_pass);

            return $model->save();
        }

        return false;
    }

    /**
     * 获取用户对象
     */
    public function getId()
    {
        if ($this->_id === false) {
            $id = Yii::$app->user->getIdentity()->id;
            $this->_id = User::findOne($id);
        }

        return $this->_id;
    }
}
