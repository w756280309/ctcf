<?php

namespace common\models\user;

use common\event\LoginEvent;
use common\models\log\LoginLog;
use common\service\LoginService;
use common\utils\SecurityUtils;
use Yii;
use yii\base\Model;
use Zii\Validator\CnMobileValidator;

class LoginForm extends Model
{
    public $phone;
    public $password;
    public $username;
    public $verifyCode;
    public $rememberMe = true;

    private $user = false;

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
            ['verifyCode', 'required', 'message' => '验证码不能为空', 'on' => ['verifycode', 'org_verifycode']],
            [
                'verifyCode',
                'string',
                'length' => 4,
                'message' => '验证码长度必须为4位',
                'on' => [
                    'verifycode',
                    'org_verifycode',
                ]
            ],
            ['verifyCode', 'captcha', 'message' => '验证码不正确', 'on' => ['verifycode', 'org_verifycode']],
            ['phone', 'string', 'length' => 11, 'message' => '手机号长度必须为11位数字'],
            ['phone', CnMobileValidator::className()],
            [
                ['username', 'password'],
                'string',
            ],
            //企业账号格式 不能是纯数字，或是纯字母
            [
                'username',
                'match',
                'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/',
                'message' => '企业账号必须为数字和字母的组合',
            ],
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
    public function login($userType, $isInApp = false)
    {
        if (false === $this->user) {
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->user = User::findOne([
                    'safeMobile' => SecurityUtils::encrypt($this->phone),
                    'type' => $userType,
                ]);
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->user = User::findOne(['username' => $this->username, 'type' => $userType]);
            }
        }

        if (!$this->user) {
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->addError('phone', '该手机号还没有注册');

                return false;
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->addError('username', '该企业账号还没有注册');

                return false;
            }
        }

        $yiiApp = Yii::$app;

        $loginEvent = new LoginEvent([
            'loginId' => $this->phone,
            'password' => $this->password,
            'user' => $this->user,
        ]);
        $yiiApp->trigger('bw.user.login.id_hit', $loginEvent);
       
        if (User::STATUS_DELETED === $this->user->status || $this->user->is_soft_deleted) {
            if (User::USER_TYPE_PERSONAL === $userType) {
                $this->addError('phone', '该用户已被锁定');

                return false;
            } elseif (User::USER_TYPE_ORG === $userType) {
                $this->addError('username', '该用户已被锁定');

                return false;
            }
        }

        if (!$this->user->validatePassword($this->password)) {
            $this->addError('password', '手机号或密码不正确');

            return false;
        }

        $isLoggedIn = $isInApp
            ? Yii::$app->user->setIdentity($this->user) || true
            : Yii::$app->user->login($this->user, $this->rememberMe ? 3600 : 0);

        if ($isLoggedIn) {
            //登录成功日志
            $login = new LoginService();
            $logintype = CLIENT_TYPE == 'pc' ? LoginLog::TYPE_PC : LoginLog::TYPE_WAP;
            $login->logFailure(User::USER_TYPE_ORG === $userType ? $this->username : $this->phone, $logintype, LoginLog::STATUS_SUCCESS);

            if (!$isInApp) {    //记录WAP，PC登录状态
                self::loginStatus();
            }
            $this->user->scenario = 'login';
            $this->user->last_login = time();

            return $this->user->save();
        }

        return false;
    }

    /**
     * 用于判断用户是否存在.
     *
     * @return bool
     */
    public function isUserExist()
    {
        return false !== $this->user;
    }
    /**
     * 记录用户的登录状态，各端只保留一个有效连接
     * 用于 wap,PC
     */
    private function loginStatus()
    {
        //记录用户登录状态，限制相同的设备多处登录
        $redis = Yii::$app->redis;
        $equipment = CLIENT_TYPE == 'pc' ? 'pc' : 'wap';
        $loginSign = Yii::$app->session->getId();
        if (!empty($equipment) && !empty($loginSign) && !empty($this->user)) {
            //当前用户是否存在登录状态   array
            $redisContent = json_decode($redis->hget('login_status_user', $this->user->id), true);
            $redisContent[$equipment] = $loginSign;
            $redis->hset('login_status_user', $this->user->id, json_encode($redisContent));
        }
    }


}
