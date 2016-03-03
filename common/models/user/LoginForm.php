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
    public $username;
    public $verifyCode;
    public $rememberMe = true;

    private $_user = false;

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            'login' => ['phone', 'password', 'rememberMe'],   //投资会员登陆
            'org_login' => ['username', 'password', 'rememberMe'],  //融资会员登陆
            'verifycode' => ['phone', 'password', 'verifyCode', 'rememberMe'],   //需要校验图形验证码 投资用户登陆
            'org_verifycode' => ['username', 'password', 'verifyCode', 'rememberMe'],   //需要校验图形验证码 融资用户登陆
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => '手机号码不能为空', 'on' => ['login', 'verifycode']],
            ['username', 'required', 'message' => '企业账号不能为空', 'on' => ['org_login', 'org_verifycode']],
            ['password', 'required', 'message' => '密码不能为空'],
            ['verifyCode', 'required', 'message' => '图形验证码不能为空', 'on' => ['org_login', 'org_verifycode']],
            ['verifyCode', 'string', 'length' => 6, 'message' => '验证码长度必须为6位', 'on' => ['org_login', 'org_verifycode']],
            ['verifyCode', 'captcha', 'on' => ['org_login', 'verifycode', 'org_verifycode']],
            ['phone', 'match', 'pattern' => '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/', 'message' => '您输入的手机号格式不正确'],
            ['phone', 'string', 'length' => 11, 'message' => '手机号长度必须为11位数字'],
            [
                ['username', 'password'],
                'string',
                'length' => [6, 20],
            ],
            //企业账号格式 不能是纯数字，或是纯字母
            ['username', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/', 'message' => '企业账号必须为数字和字母的组合'],
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
            'username' => '企业账号',
            'phone' => '手机号码',
            'password' => '密码',
            'verifyCode' => '',
        ];
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
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->_user = User::findOne(['mobile' => $this->phone, 'type' => $userType]);
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->_user = User::findOne(['username' => $this->username, 'type' => $userType]);
            }
        }

        if (!$this->_user) {
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->addError('phone', '该手机号还没有注册');

                return false;
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->addError('username', '该企业账号还没有注册');

                return false;
            }
        } elseif (User::STATUS_DELETED === $this->_user->status) {
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->addError('phone', '该用户已被锁定');

                return false;
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->addError('username', '该用户已被锁定');

                return false;
            }
        }

        if (!$this->_user->validatePassword($this->password)) {
            $this->addError('password', '密码不正确');

            return false;
        }

        if (Yii::$app->user->login($this->_user, $this->rememberMe ? 3600 : 0)) {
            $this->_user->scenario = 'login';
            $this->_user->last_login = time();

            return $this->_user->save();
        }

        return false;
    }
}
