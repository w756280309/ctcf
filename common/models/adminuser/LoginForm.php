<?php
namespace common\models\adminuser;

use common\models\adminuser\Admin;

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
            ['username', 'required', 'message' => '用户名不能为空!'],
            ['password', 'required', 'message' => '密码不能为空!'],
            [
                'username',
                'string',
                'length' => [5, 16],
                'tooShort' => "用户名太短了,用户名要大于5位！",
                'tooLong' => "用户名超长了,用户名要小于等于16位！"
            ],
            [
                'password',
                'string',
                'length' => [6, 12],

            ],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
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
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '用户名和密码不正确!');
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
            $res = Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
            //记录登录状态
            self::loginStatus();
            return $res;
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
            $this->_user = Admin::findByUsername($this->username);
        }

        return $this->_user;
    }
    //记录管理员登录状态
    private function loginStatus()
    {
        $redis = Yii::$app->redis;
        $sign = Yii::$app->session->getId();
        if (!empty($sign) && !empty($this->user)) {
            $redis->hset('login_status_admin', $this->user->id, $sign);
        }
    }
}
