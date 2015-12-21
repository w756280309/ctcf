<?php

namespace common\models\adv;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "adv_pos".
 *
 */
class AdvPos extends ActiveRecord
{
    //0,正常，1不显示
    const STATUS_SHOW = 0;
    const STATUS_HIDDEN = 1;
    
    //0正常，1删除
    const DEL_STATUS_SHOW = 0;
    const DEL_STATUS_DEL = 1;
    
    const POS_HOME_HEAD="HOME_HEAD";
    const POS_HOME_NEWS_LEFT="HOME_NEWS_LEFT";
    const POS_LOGIN_LEFT="LOGIN_LEFT";
    const POS_HOME_MIDDLE="HOME_MIDDLE";
    const POS_HOME_FOOT_PARTNER="HOME_FOOT_PARTNER";
    const POS_REG_LEFT="REG_LEFT";
    
    public static function getStatusList(){
        return array(
            self::STATUS_SHOW => '显示',
            self::STATUS_HIDDEN => '隐藏'
        );
    }
    
    public static function getDelStatusList(){
        return array(
            self::DEL_STATUS_SHOW => '正常',
            self::DEL_STATUS_DEL => '删除'
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adv_pos';
    }

    public function scenarios() {
        return [
            'update' => ['id', 'title','code', 'width', 'height', 'number','status','del_status'],
            'create' => ['title','code', 'width', 'height', 'number','status','del_status'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors() {
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
            [['title', 'width', 'height', 'number'], 'required', 'on' => ['create', 'update']],
            [['code'], 'required', 'on' => ['create']],
            ['status', 'default', 'value' => self::STATUS_SHOW, 'on' => ['create']],
            ['del_status', 'default', 'value' => self::DEL_STATUS_SHOW, 'on' => ['create']]
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '位置描述',
            'code' => "编码",
            'width' => '宽度',
            'height' => '高度',
            'number' => '显示图片数',
            'status' => '是否显示',
            'del_status' => '是否删除',
            'creator_id' => '创建者管理员id',
            'updated_at' => '更新时间',
            'created_at' => '添加时间',
        ];
    }
}
