<?php

namespace common\models\adminuser;

use Wcg\Xii\Crm\Model\AdminInterface;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin".
 *
 * @property int $id
 * @property string $username
 * @property string $real_name
 * @property string $email
 * @property string $password_hash
 * @property string $auth_key
 * @property int $status
 * @property int $updated_at
 * @property int $created_at
 */
class Admin extends \yii\db\ActiveRecord implements IdentityInterface, AdminInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    public $user_pass = '';
    public $auths;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin';
    }

    public function scenarios()
    {
        return [
            'register' => ['id', 'username', 'email', 'real_name', 'status', 'password_hash', 'user_pass', 'role_sn', 'auths', 'udesk_email'],
            'active' => ['status'],
            'log' => ['last_login_ip', 'last_login_time'],
            'editpass' => ['password_hash'],
        ];
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
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_ACTIVE, 'on' => 'register'],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
            [['username', 'email'], 'trim'],
            [['username', 'email', 'role_sn'], 'required', 'on' => 'register'],
            ['auths', 'required', 'when' => function($model) {
                return 'R001' !== $model->role_sn;
            }, 'whenClient' => "function (attribute, value) {
                return $('#admin-role_sn').val() !== 'R001';
            }", 'on' => 'register'],
            [['role_sn'], 'compare', 'compareValue' => 0, 'operator' => '!=', 'message' => '请选择角色!'],
            ['email', 'email', 'message' => ' 必须为email格式', 'on' => 'register'],
            ['udesk_email', 'email', 'message' => ' 必须为email格式', 'on' => 'register'],
            [['status'], 'integer', 'on' => 'register'],
            [
                'username',
                'string',
                'length' => [5, 16],
                'on' => 'register',
            ],
            [['real_name'], 'string', 'max' => 20],
            [['email'], 'string', 'max' => 50],
            [['password_hash', 'auth_key'], 'string', 'max' => 128],
            ['last_login_time', 'default', 'value' => time(), 'on' => 'log'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '管理员用户名',
            'real_name' => '管理员姓名',
            'auths' => '权限',
            'role_sn' => '角色',
            'email' => '管理员Email',
            'password_hash' => '用户密码hash',
            'auth_key' => 'cookie权限认证key',
            'status' => '状态', // 0-锁定 1-正常
            'updated_at' => '创建时间',
            'created_at' => '更新时间',
            'user_pass' => '密码',
            'udesk_email' => 'udesk外呼邮箱'
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
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
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
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
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

    /**
     * 根据role_sn获得对应群组的后台用户信息
     *
     * @param $roleSn
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findByRoleSn($roleSn)
    {
        $r = Role::tableName();
        $a = Admin::tableName();
        return Admin::find()
            ->innerJoin($r, "$r.sn = $a.role_sn")
            ->where(["$a.status" => self::STATUS_ACTIVE])
            ->andWhere(["$a.role_sn" => $roleSn])
            ->all();
    }
}
