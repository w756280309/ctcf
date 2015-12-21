<?php
namespace common\models\user;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_type".
 *
 */
class UserType extends ActiveRecord
{
    //0不显示，1正常
    const STATUS_SHOW = 1;
    const STATUS_HIDDEN = 0;
    
    const UPLOAD_PATH = "/upload/user/";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_type';
    }

    public function scenarios() {
        return [
            'update' => ['id', 'name', 'status', 'creator_id', 'updated_at','created_at'],
            'create' => ['name', 'status', 'creator_id', 'updated_at','created_at'],
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
            [['name'], 'required', 'on' => ['update']],
            ['status', 'default', 'value' => self::STATUS_SHOW, 'on' => ['create']],
            ['name','required','message'=>'用户名不能为空'],
            ['name','unique','message'=>'用户名已占用']
        ];
    }
     
}
