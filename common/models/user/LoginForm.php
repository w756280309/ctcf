<?php
namespace common\models\user;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $phone;
    public $password;
    public $verifyCode;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'verifycode' => ['verifyCode'],   //需要校验图形验证码
        ];        
    }  
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['phone', 'required', 'message' => '手机号码不能为空'],
            ['password', 'required', 'message' => '密码不能为空'],
            ['verifyCode', 'required', 'message' => '图形验证码不能为空', 'on' => 'verifycode'],
            ['verifyCode', 'captcha', 'on' => 'verifycode'],
            [['phone'],'match','pattern'=>'/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/','message'=>'您输入的手机号不正确'],
            [['phone'],'checkPhone'],
            [
                'password',
                'string',
                'length' => [6, 20],

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
            'phone' => '手机号码',
            'password' => '密码',
        ];
    }
    
    /**
     * 检查手机号是否已经注册过
     * @param type $attribute
     * @param type $params
     * @return boolean
     */
    public function checkPhone($attribute,$params){
        $num = $this->$attribute;
        $re = User::findOne(['mobile'=>$num]);
        if(empty($re) || $re->type != User::USER_TYPE_PERSONAL) {
            $this->addError($attribute, "该手机号还没有注册");        
        } else if($re->status == User::STATUS_DELETED) {
            $this->addError($attribute, "该用户已被锁定"); 
        } else {
            return true; 
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
                $this->addError($attribute, '密码不正确');
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
        $user = $this->getUser();
        if (Yii::$app->user->login($user, $this->rememberMe ? 3600 * 24 * 30 : 0)) {
            $user->scenario = 'login';
            $user->last_login = time();
            return $user->save();
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
            $this->_user = User::findByUsername('',$this->phone);
        }

        return $this->_user;
    }


}
