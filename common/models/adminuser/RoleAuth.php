<?php

namespace common\models\adminuser;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "role_auth".
 *
 * @property integer $id
 * @property string $role_sn
 * @property string $auth_sn
 * @property string $auth_name
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class RoleAuth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'role_auth';
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
            [['role_sn', 'auth_sn', 'auth_name'], 'required'],
            [['status', 'updated_at', 'created_at'], 'integer'],
            [['role_sn', 'auth_sn'], 'string', 'max' => 24],
            [['auth_name'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_sn' => 'Role Sn',
            'auth_sn' => 'Auth Sn',
            'auth_name' => 'Auth Name',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
