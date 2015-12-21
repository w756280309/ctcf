<?php

namespace common\models\adminuser;

use Yii;

 /* 
 * @property integer $id
 * @property string $user
 * @property string $tel
 * @property string $login_time
 * @property string $status
 * @property integer $created_at
 */
class Test extends \yii\db\ActiveRecord {

    //0不显示，1正常
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;

    /**
     * @inheritdoc
     */
    public static function tableName() {
	return 'test';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
	return [
	    [['user','tel','login_time','created_at','status'], 'required'],
	    [['tel'], 'string', 'max' => 11],
	    [['user'], 'unique']
	];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
	return [
	    'id' => 'ID',
	    'user' => '用户名',
	    'tel' => '手机号码',
	    'login_time' => '登陆时间',
	    'status' => '状态',
	    'created_at' => '创建时间',
	];
    }
    
     public static function findIdentity($id)
        {
            return static::findOne(['id' => $id]);
        }

}
