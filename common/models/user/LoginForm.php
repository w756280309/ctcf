<?php

namespace common\models\user;

use Yii;
use yii\base\Model;

/**
 * Login form.
 */
class LoginForm extends Model
{
    public $phone;
    public $password;
    public $verifyCode;
    public $rememberMe = true;

    private $_user = false;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            'login' => ['phone', 'password', 'rememberMe'],
            'verifycode' => ['phone', 'password', 'verifyCode', 'rememberMe'],   //需要校验图形验证码
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => '手机号码不能为空'],
            ['password', 'required', 'message' => '密码不能为空'],
            ['verifyCode', 'required', 'message' => '图形验证码不能为空', 'on' => 'verifycode'],
            ['verifyCode', 'string', 'length' => 6, 'message' => '验证码长度必须为6位', 'on' => 'verifycode'],
            ['verifyCode', 'captcha', 'on' => 'verifycode'],
            ['phone', 'match', 'pattern' => '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/', 'message' => '您输入的手机号格式不正确'],
            ['phone', 'string', 'length' => 11, 'message' => '手机号长度必须为11位数字'],
            [
                'password',
                'string',
                'length' => [6, 20],
            ],
            //验证密码格式 不能是纯数字，或是纯字母
            ['password', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/', 'message' => '密码必须为数字和字母的组合'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'phone' => '手机号码',
            'password' => '密码',
            'verifyCode' => '',
        ];
    }

    /**
     * 检查手机号对应的账户是否符合规范.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkPhone()
    {
        if (empty($this->_user)) {
            $this->addError('phone', '该手机号还没有注册');

            return false;
        } elseif (User::STATUS_DELETED === $this->_user->status) {
            $this->addError('phone', '该用户已被锁定');

            return false;
        } else {
            return true;
        }
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword()
    {
        if (!$this->_user || empty($this->password)) {
            return false;
        }

        if (!$this->_user->validatePassword($this->password)) {
            $this->addError('password', '密码不正确');

            return false;
        }

        return true;
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @param int $userType 用户类型 1 投资用户 2 融资用户
     *
     * @return bool whether the user is logged in successfully
     */
    public function login($userType)
    {
        if (false === $this->_user) {
            $this->_user = User::findOne(['mobile' => $this->phone, 'type' => $userType]);
        }

        if ($this->checkPhone() && $this->validatePassword() && Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 : 0)) {
            $this->_user->scenario = 'login';
            $this->_user->last_login = time();

            return $this->_user->save();
        } else {
            return false;
        }
    }
}
