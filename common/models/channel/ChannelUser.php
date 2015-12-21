<?php

namespace common\models\channel;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;
/**
 * This is the model class for table "channel_user".
 *
 * @property string $id
 * @property integer $channel_id
 * @property string $channel_user_sn
 * @property string $username
 * @property string $usercode
 * @property string $mobile
 * @property string $email
 * @property string $real_name
 * @property string $idcard
 * @property string $tel
 * @property string $law_master
 * @property string $law_master_idcard
 * @property string $business_licence
 * @property string $org_name
 * @property string $org_code
 * @property string $shui_code
 * @property string $org_url
 * @property string $init_pwd
 * @property integer $init_pwd_status
 * @property string $password_hash
 * @property string $auth_key
 * @property integer $status
 * @property string $updated_at
 * @property string $created_at
 */
class ChannelUser extends \yii\db\ActiveRecord implements IdentityInterface
{

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'channel_user';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['channel_id', 'init_pwd_status', 'status'], 'integer'],
            ['usercode','unique','message'=>'usercode已占用'],
            [['username', 'usercode','channel_id','channel_user_sn'], 'required'],//,'mobile'
            [['channel_user_sn', 'tel', 'law_master_idcard', 'org_code'], 'string', 'max' => 30],
            [['username', 'usercode'], 'string', 'max' => 32],
            //[['mobile'], 'string', 'max' => 11],
            [['email', 'real_name', 'idcard', 'law_master', 'business_licence', 'shui_code', 'org_url'], 'string', 'max' => 50],
            [['org_name'], 'string', 'max' => 150],
            [['init_pwd', 'password_hash', 'auth_key'], 'string', 'max' => 128],
            //身份证号码为15位或者18位，15位时全为数字，18位前17位为数字，最后一位是校验位，可能为数字或字符X
//            [['idcard','law_master_idcard'],'match','pattern'=>'/(^\d{15}$)|(^\d{17}(\d|X)$)/','message'=>'{attribute}身份证号码不正确,必须为15位或者18位'],
//            [['idcard','law_master_idcard'],'checkIdNumber'],
            //[['mobile'],'match','pattern'=>'/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/','message'=>'手机号格式错误'],
        ];
    }
    
    /**
     * 验证身份证号生日
     * @param type $attribute
     * @param type $params
     * @return boolean
     */
    public function checkIdNumber($attribute,$params){
        $num = $this->$attribute;
        $tmpStr="";
        if (strlen($num) == 15) {  
            $tmpStr = substr($num, 6, 6);  
            $tmpStr = "19" . $tmpStr;  
            $tmpStr = substr($tmpStr, 0, 4) . "-" . substr($tmpStr, 4, 2) . "-" . substr($tmpStr, 6);  
        } else {  
            $tmpStr = substr($num, 6, 8);  
            $tmpStr = substr($tmpStr, 0, 4) . "-" . substr($tmpStr, 4, 2) . "-" . substr($tmpStr, 6);  
        }  
        
        $reDate = '/(([0-9][9][2-9][0-9])-(((0[13578]|1[02])-(0[1-9]|[12][0-9]|3[01]))|((0[469]|11)-(0[1-9]|[12][0-9]|30))|(02-(0[1-9]|[1][0-9]|2[0-8]))))|((([0-9]{2})(0[48]|[2468][048]|[13579][26])|((0[48]|[2468][048]|[3579][26])00))-02-29)/'; 
        $re = preg_match($reDate, $tmpStr);
        if($re){
            return true;
        }else{
            $this->addError($attribute, "身份证号错误");  
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'channel_id' => '渠道编号',
            'channel_user_sn' => '渠道用户编号',
            'username' => '用户名',
            'usercode' => '用户编号',
            'mobile' => '手机',
            'email' => '邮箱',
            'real_name' => '真实姓名',
            'idcard' => '身份证',
            'tel' => '电话',
            'law_master' => 'Law Master',
            'law_master_idcard' => 'Law Master Idcard',
            'business_licence' => 'Business Licence',
            'org_name' => 'Org Name',
            'org_code' => 'Org Code',
            'shui_code' => 'Shui Code',
            'org_url' => 'Org Url',
            'init_pwd' => '初始密码',
            'init_pwd_status' => '初始密码状态',
            'password_hash' => '密码',
            'auth_key' => 'Auth Key',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
    
    /**
     * 渠道用户编号
     * @param type $channel_id
     * @param type $pre
     * @param type $type
     * @return type
     */
    public static function createChannelUserCode($channel_id=1,$pre="JDD",$type=1){
        $cond = array();
        if($type==1){
            $cond = ['type'=>1,'channel_id'=>$channel_id];
        }else{
            $cond = ['type'=>2,'channel_id'=>$channel_id];
        }
        $count = static::find()->where($cond)->count()+1;
        $code = $type;
        for($i=0;$i<6-strlen($count);$i++){
            $code .= "0";
        }
        return $pre.$code.$count;
    }

        /**
     * 
     * @param type $len 长度
     * @param type $simple 1 简单 2 复杂
     * @return type
     */
    public static function createRandomStr($len = 6, $simple = 1) {
        if(!in_array($simple, array(1,2))){
            return FALSE;
        }
        $str = '';
        $chars = "";
        if($simple==1){
            $chars = '0123456789';    
        }else{
            $chars = 'abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHIJKMNPQRSTUVWXYZ'; //去掉1跟字母l防混淆      
        }   
        if ($len > strlen($chars)) {//位数过长重复字符串一定次数
            $chars = str_repeat($chars, ceil($len / strlen($chars)));
        }
        $chars = str_shuffle($chars); //打乱字符串
        $str = substr($chars, 0, $len);
        return $str;
    }
      /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setReturnPassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
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
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByCond($cond = array())
    {
        return static::findOne($cond);
    }

        /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
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


}
