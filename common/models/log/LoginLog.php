<?php

namespace common\models\log;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "login_log".
 *
 * @property string $id
 * @property string $ip
 * @property integer $type
 * @property string $user_name
 * @property integer $updated_at
 * @property integer $created_at
 */
class LoginLog extends \yii\db\ActiveRecord
{
    const TYPE_WAP = 1;
    const TYPE_PC = 2;
    const TYPE_BACKEND = 3;

    const STATUS_SUCCESS = true;   //登陆成功
    const STATUS_ERROR = false; //登录失败
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'login_log';
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
            ['status', 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'IP地址',
            'type' => '渠道类型：1代表前台wap;2代表前台pc端;3代表后端控制台',
            'user_name' => '用户登陆名',
            'updated_at' => '记录更新时间',
            'created_at' => '记录创建时间',
        ];
    }
}
