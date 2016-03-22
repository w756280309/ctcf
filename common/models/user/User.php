<?php

namespace common\models\user;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
use P2pl\Borrower;
use common\models\epay\EpayUser;
use P2pl\UserInterface;
use YiiPlus\Validator\CnMobileValidator;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int $type
 * @property string $username
 * @property string $mobile
 * @property string $email
 * @property string $real_name
 * @property string $idcard
 * @property string $org_name
 * @property string $org_code
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property int $bank_card_status
 * @property int $email_status
 * @property int $mobile_status
 * @property int $idcard_status
 * @property int $updated_at
 * @property int $created_at
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface, UserInterface
{
    use \YiiPlus\Model\ErrorExTrait;

    //会员类型 1：普通会员 ， 2：融资会员
    const USER_TYPE_PERSONAL = 1;
    const USER_TYPE_ORG = 2;
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const EXAMIN_STATUS_UNPASS = -1;
    const EXAMIN_STATUS_WAIT = 0;
    const EXAMIN_STATUS_PASS = 1;
    const EMAIL_STATUS_UNPASS = -1;
    const EMAIL_STATUS_WAIT = 0;
    const EMAIL_STATUS_PASS = 1;
    const MOBILE_STATUS_UNPASS = -1;
    const MOBILE_STATUS_WAIT = 0;
    const MOBILE_STATUS_PASS = 1;
    const IDCARD_STATUS_UNPASS = -1;
    const IDCARD_STATUS_WAIT = 0;
    const IDCARD_STATUS_PASS = 1;
    const KUAIJIE_STATUS_Y = 1;
    const KUAIJIE_STATUS_N = 0;
    const IDCARD_EXAMIN_COUNT = 3;

    const QPAY_NONE = 0;//未绑卡
    const QPAY_ENABLED = 1;//已经绑卡
    const QPAY_PENDING = 2;//绑卡中

    public static function examinStatus($key = null)
    {
        $arr = array(
            self::EXAMIN_STATUS_UNPASS => '未通过',
            self::EXAMIN_STATUS_WAIT => '待审核',
            self::EXAMIN_STATUS_PASS => '审核通过',
        );
        if (!is_null($key)) {
            return $arr[$key];
        }

        return $arr;
    }

    /**
     * 生成用户编号非渠道.
     *
     * @param type $type 1个人 9机构
     */
    public static function createCode($type = 1)
    {
        $cond = array();
        if ($type == 1) {
            $cond = ['type' => 1, 'channel_id' => 0];
        } else {
            $cond = ['type' => 2, 'channel_id' => 0];
        }
        $count = static::find()->where($cond)->count() + 1;
        $code = $type;
        for ($i = 0; $i < 6 - strlen($count); ++$i) {
            $code .= '0';
        }

        return $code.$count;
    }

    /**
     * 渠道用户编号.
     *
     * @param type $channel_id
     * @param type $pre
     * @param type $type
     *
     * @return type
     */
    public static function createChannelUserCode($channel_id = 1, $pre = 'JDD', $type = 1)
    {
        $cond = array();
        if ($type == 1) {
            $cond = ['type' => 1, 'channel_id' => $channel_id];
        } else {
            $cond = ['type' => 2, 'channel_id' => $channel_id];
        }
        $count = static::find()->where($cond)->count() + 1;
        $code = $type;
        for ($i = 0; $i < 6 - strlen($count); ++$i) {
            $code .= '0';
        }

        return $pre.$code.$count;
    }

    /**
     * @param type $len    长度
     * @param type $simple 1 简单 2 复杂
     *
     * @return type
     */
    public static function createRandomStr($len = 6, $simple = 1)
    {
        if (!in_array($simple, array(1, 2))) {
            return false;
        }
        $str = '';
        $chars = '';
        if ($simple == 1) {
            $chars = '0123456789';
        } else {
            $chars = 'abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHIJKMNPQRSTUVWXYZ'; //去掉1跟字母l防混淆
        }
        if ($len > strlen($chars)) {
            //位数过长重复字符串一定次数
            $chars = str_repeat($chars, ceil($len / strlen($chars)));
        }
        $chars = str_shuffle($chars); //打乱字符串
        $str = substr($chars, 0, $len);

        return $str;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return [
            'add' => ['type', 'username', 'password_hash', 'usercode', 'mobile', 'email', 'real_name', 'idcard', 'org_name', 'org_code', 'status', 'auth_key', 'user_pass', 'in_time', 'cat_id', 'law_master', 'law_master_idcard', 'law_mobile', 'shui_code', 'business_licence', 'tel', "mianmiStatus"
            ],
            'edit' => ['id', 'type', 'username', 'mobile', 'email', 'real_name', 'idcard', 'org_name', 'org_code', 'status', 'auth_key', 'user_pass', 'in_time', 'cat_id', 'law_master', 'law_master_idcard', 'law_mobile', 'shui_code', 'business_licence', 'tel', 'passwordLastUpdatedTime',
            ],
            'signup' => ['type', 'modile', 'password_hash', 'auth_key', 'usercode'],
            'idcardrz' => ['real_name', 'idcard', 'idcard_status'],
            'editpass' => ['password_hash', 'trade_pwd', 'auth_key'],
            'login' => ['last_login'],
            'kuaijie' => ['kuaijie_status'],
        ];
    }

    public function rules()
    {
        return [
            [['username', 'usercode', 'email'], 'trim'],
            [['type', 'status', 'updated_at', 'created_at', 'kuaijie_status'], 'integer'],
            [
                'username',
                'string',
                'length' => [6, 20],
            ],
            //企业账号格式 不能是纯数字，或是纯字母
            ['username', 'match', 'pattern' => '/(?!^\d+$)(?!^[a-zA-Z]+$)^[0-9a-zA-Z]{6,20}$/', 'message' => '企业账号必须为数字和字母的组合'],
            [['username'], 'unique', 'message' => '该企业账户号已被占用'],
            [
                'usercode',
                'string',
                'length' => [5, 16],
            ],
            [['mobile', 'username', 'real_name', 'idcard'], 'required'],
            [['mobile'], 'unique', 'message' => '该手机号码已被占用，请重试', 'on' => 'add'],
            //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
            [['idcard', 'law_master_idcard'], 'match', 'pattern' => '/(^\d{15}$)|(^\d{17}(\d|X)$)/', 'message' => '{attribute}身份证号码不正确,必须为15位或者18位'],
            [['real_name'], 'string', 'max' => 50, 'on' => 'idcardrz'],
            [['idcard'], 'string', 'length' => 18, 'on' => 'idcardrz'],
            [['idcard', 'law_master_idcard'], 'checkIdNumber'],
            [['idcard_status', 'email_status', 'mobile_status', "mianmiStatus"], 'default', 'value' => 0],
            [['mobile', 'new_mobile'], CnMobileValidator::className()],
            [['mobile'], 'string', 'max' => 11],
            [['usercode'], 'unique', 'message' => '会员编号已占用'],
            [['email'], 'unique', 'message' => 'Email已占用'],
            [['email', 'real_name'], 'string', 'max' => 50],
            [['org_name'], 'string', 'max' => 150],
            [['org_code'], 'string', 'max' => 30],
            [['status'], 'default', 'value' => 1],
            [['password_hash', 'trade_pwd', 'auth_key'], 'string', 'max' => 128],
            [['real_name', 'idcard'], 'required', 'on' => 'idcardrz'],
            [['idcard'], 'checkIdNumberUnique', 'on' => 'idcardrz'],
            [['idcard'], 'match', 'pattern' => '/(^\d{15}$)|(^\d{17}(\d|X)$)/', 'message' => '{attribute}身份证号码不正确,必须为15位或者18位', 'on' => 'idcardrz'],
            [['tel'], 'match', 'pattern' => '/^[0-9\-]{6,16}$/', 'message' => '格式不正确，应为数字和中划线', 'on' => ['add', 'edit']],
            [['org_code'], 'match', 'pattern' => '/[a-zA-Z0-9-]/', 'message' => '格式不正确，应为字母数字中划线', 'on' => ['add', 'edit']],
            [['business_licence', 'shui_code'], 'match', 'pattern' => '/\d+/', 'message' => '格式不正确，应为纯数字格式', 'on' => ['add', 'edit']],
            [['org_name'], 'required'],
            [['passwordLastUpdatedTime'], 'safe'],
        ];
    }

    /**
     * 验证身份证号生日.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkIdNumber($attribute, $params)
    {
        $num = $this->$attribute;
        $tmpStr = '';
        if (strlen($num) == 15) {
            $tmpStr = substr($num, 6, 6);
            $tmpStr = '19'.$tmpStr;
            $tmpStr = substr($tmpStr, 0, 4).'-'.substr($tmpStr, 4, 2).'-'.substr($tmpStr, 6);
        } else {
            $tmpStr = substr($num, 6, 8);
            $tmpStr = substr($tmpStr, 0, 4).'-'.substr($tmpStr, 4, 2).'-'.substr($tmpStr, 6);
        }

        $reDate = '/(([0-9][9][2-9][0-9])-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8]))))|((([0-9]{2})(0[48]|[2468][048]|[13579][26])|((0[48]|[2468][048]|[3579][26])00))-02-29)/';
        $re = preg_match($reDate, $tmpStr);
        if ($re) {
            return true;
        } else {
            $this->addError($attribute, '身份证号错误');
        }
    }

    /**
     * 验证身份证号唯一性.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkIdNumberUnique($attribute, $params)
    {
        $num = $this->$attribute;
        $data = self::find()->where(['idcard' => $num])->one();
        if ($data) {
            $this->addError($attribute, '该身份证号已被占用');
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => '会员类别 ', //1-普通用户 2-机构用户
            'username' => '企业账号',
            'usercode' => '会员编号',
            'mobile' => '联系手机号',
            'email' => 'Email',
            'cat_id' => '会员分类',
            'real_name' => '姓名',
            'idcard' => '身份证号',
            'law_master' => '企业法人姓名',
            'law_master_idcard' => '企业法人身份证',
            'org_name' => '机构名称',
            'org_code' => '组织机构代码证号',
            'in_time' => '入会时间',
            'tel' => '办公电话',
            'business_licence' => '营业执照号',
            'shui_code' => '税务登记证号',
            'org_url' => '机构网址',
            'password_hash' => '用户密码hash',
            'f_trade_pwd' => '交易密码',
            'confirm_trade_pwd' => '确认交易密码',
            'auth_key' => 'cookie权限认证key',
            'status' => '会员状态 ', //0-锁定 1-正常
            'idcard_examin_count' => '身份证审核次数',
            'updated_at' => '注册时间',
            'created_at' => '更新时间',
            'user_pass' => '会员密码',
            'verifyCode' => '验证码',
            'new_mobile' => '手机号',
            'password' => '密码',
            'password_confirm' => '确认密码',
            'sms_code' => '短信验证码',
            'agree' => '',
            'old_password' => '原密码', 'new_password' => '新密码', 'new_confirm_password' => '确认密码',
            'f_pwd' => '重置密码', 'c_f_pwd' => '确认密码',
            'trade_pwd' => '交易密码', 'old_trade' => '原交易密码',
            'new_trade' => '新交易密码', 'new_trade_confirm' => '确认交易密码',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByUsername($username, $mobile = null)
    {
        if ($mobile) {
            return static::findOne(['mobile' => $mobile, 'status' => self::STATUS_ACTIVE]);
        }

        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username.
     *
     * @param string $username
     *
     * @return static|null
     */
    public static function findByCond($cond = array())
    {
        return static::findOne($cond);
    }

    public static function findByUsercode($usercode)
    {
        return static::findOne(['username' => $usercode, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token.
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return;
        }

        return static::findOne([
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid.
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);

        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Validates trade_pwd.
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     */
    public function validateTradePwd($password, $oldtradepwd)
    {
        return Yii::$app->security->validatePassword($password, $oldtradepwd);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setTradePassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
        //$this->setAttribute('trade_pwd', Yii::$app->security->generatePasswordHash($password));
    }

    /**
     * Generates "remember me" authentication key.
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token.
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString().'_'.time();
    }

    /**
     * Removes password reset token.
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->getSecurity()->generateRandomString();
            }

            return true;
        }

        return false;
    }

    //通过固定格式生成可以升序的用户编号
    //如 WDJFQY0001 --> WDJFQY0002,
    public static function create_code($field = 'usercode', $code = 'WDJF', $length = 4, $pad_length = 9)
    {
        //取到所找字段的最大值，如WDJFQY001 WDJFQY003 筛选出的结果应是WDJFQY003
        if ('WDJF' === $code) {
            $maxValue = self::find()->where(['type' => self::USER_TYPE_PERSONAL])->max($field);
        } else {
            $maxValue = self::find()->where(['type' => self::USER_TYPE_ORG])->max($field);
        }
        //若数据库中该字段没有值，就使用默认字符WDJFQY
        $usercode = $maxValue ? $maxValue : $code;
        $num = 1;
        if (preg_match('/0+(\d+)$/', $usercode, $matches)) {
            $num = intval($matches[1]) + 1;
        }
        //选出编号中的数字
        //选出编号中的字符
        //取出的数字，加一后，在左边填充成4为，然后与字符相加
        return $code.str_pad($num, $pad_length, '0', STR_PAD_LEFT);
    }

    /**
     * 获取投资账户.
     *
     * @return UserAccount
     */
    public function getLendAccount()
    {
        return $this->hasOne(UserAccount::className(), ['uid' => 'id'])
                ->where(['type' => UserAccount::TYPE_LEND]);
    }

    /**
     * 获取融资账户.
     *
     * @return UserAccount
     */
    public function getBorrowAccount()
    {
        return $this->hasOne(UserAccount::className(), ['uid' => 'id'])
                ->where(['type' => UserAccount::TYPE_BORROW]);
    }

    /**
     * 获取绑卡相关信息.
     *
     * @return UserBanks
     */
    public function getQpay()
    {
        return $this->hasOne(UserBanks::className(), ['uid' => 'id']);
    }

    /**
     * 获取是否是实名认证
     */
    public function ensureIdVerified()
    {
        return (self::IDCARD_STATUS_PASS === $this->idcard_status) ? true : false;
    }

    /**
     * 获取是否设置交易密码
     */
    public function ensureTxPassSet()
    {
        return ('' === $this->trade_pwd) ? false : true;
    }

    /**
     * 获取是否设置快捷卡
     */
    public function ensureQpayEnabled()
    {
        return (null === $this->qpay) ? false : true;
    }

    /**
     * 获取银行卡分支行信息.
     */
    public function ensureQpayInfoEnabled()
    {
        if (null === $this->qpay) {
            return false;
        }
        if (empty($this->qpay->sub_bank_name) || empty($this->qpay->province) || empty($this->qpay->city)) {
            return false;
        }

        return true;
    }

    /**
     * 返回总的.
     *
     * @return bool
     */
    public function ensure()
    {
        return ($this->ensureIdVerified() && $this->ensureQpayEnabled() && $this->ensureTxPassSet()) ? true : false;
    }

    /**
     * 返回联动借款人对象
     *
     * @param type $user
     *
     * @return Borrower
     *
     * @throws Exception
     */
    public static function ensureBorrower($user)
    {
        if (self::USER_TYPE_ORG !== (int) $user->type) {
            throw new Exception('不是融资人');
        }

        return new Borrower($user->id);
    }

    /**
     * 获取用户托管方平台信息.
     */
    public function getEpayUser()
    {
        return $this->hasOne(EpayUser::className(), ['appUserId' => 'id']);
    }

    public function getUserId()
    {
        return $this->id;
    }

    public function getLegalName()
    {
        return $this->real_name;
    }

    public function getIdNo()
    {
        return $this->idcard;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getEpayUserId()
    {
        return $this->epayUser->epayUserId;
    }
}
