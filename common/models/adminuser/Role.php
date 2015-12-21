<?php

namespace common\models\adminuser;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property integer $id
 * @property string $role_name
 * @property string $role_description
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class Role extends \yii\db\ActiveRecord {

    public $auths="";




	//0不显示，1正常
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;




    /**
     * @inheritdoc
     */
    public static function tableName() {
	return 'role';
    }

    public function scenarios() {
        return [
            'edit' => ['role_name', 'role_description','sn','auths','status'],
            'line' => ['status']
            ];
    }
    /**
     * @inheritdoc
     */
    public function rules() {
	return [
	    [['role_name', 'role_description','sn','auths'], 'required'],
	    ['sn','unique','message'=>'编号已占用'],
	    [['status', 'updated_at', 'created_at'], 'integer'],
	    [['role_name'], 'string', 'max' => 50],
	    [['role_description'], 'string', 'max' => 100],
	];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
	return [
	    'id' => 'ID',
	    'sn' => '编号',
	    'auths'=>"权限",
	    'role_name' => '角色名称',
	    'role_description' => '角色描述',
	    'status' => '状态',
	    'updated_at' => 'Updated At',
	    'created_at' => 'Created At',
	];
    }

}
