<?php

namespace common\models\channel;

use common\models\channel\ChannelUser;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'trim'],
            ['username', 'required', 'message' => '用户名不能为空!'],
            ['password', 'required', 'message' => '密码不能为空!'],
            [
                'username',
                'string',
                'length' => [5, 18],
                'tooShort' => "用户名太短了",
                'tooLong' => "用户名超长了"
            ],
            [
                'password',
                'string',
                'length' => [6, 12],
                'tooShort' => "密码太短了",
                'tooLong' => "密码超长了"
            ],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePasswordNew'],//由于是明码登录，取消hash密码加密的验证
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [

            'username' => '用户名',
            'password' => '密码',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePasswordNew($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || $this->password!=$user->password_hash) {
                $this->addError($attribute, '密码或用户名错误.');
            }
        }
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
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '密码或用户名错误.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    
    /**
     * 名码登录
     *
     * @return boolean whether the user is logged in successfully
     */
    public function loginming()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    
    
   /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login2()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser2(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
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
            $this->_user = ChannelUser::findByUsername($this->username);
        }

        return $this->_user;
    }
    
    
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser2()
    {
        //var_dump($cond,$this->_user);exit;
        if ($this->_user === false) {
            $this->_user = User::findByCond(['status'=>User::STATUS_ACTIVE,'channel_id'=>0,'username'=>$this->username]);
            //var_dump($this->_user);exit;
        }

        return $this->_user;
    }
    
}
