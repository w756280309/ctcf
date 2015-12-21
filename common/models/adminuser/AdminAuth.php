<?php

namespace common\models\adminuser;

use Yii;

/**
 * This is the model class for table "user_auth".
 *
 * @property integer $id
 * @property integer $admin_id
 * @property string $role_sn
 * @property string $auth_sn
 * @property string $auth_name
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class AdminAuth extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'admin_auth';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['admin_id', 'auth_name'], 'required'],
            [['admin_id', 'status', 'updated_at', 'created_at'], 'integer'],
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
            'admin_id' => 'Admin ID',
            'role_sn' => 'Role Sn',
            'auth_sn' => 'Auth Sn',
            'auth_name' => 'Auth Name',
            'status' => 'Status',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }
}
