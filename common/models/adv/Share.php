<?php

namespace common\models\adv;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "share".
 *
 * @property integer $id
 * @property string $shareKey
 * @property string $title
 * @property string $description
 * @property string $imgUrl
 * @property integer $created_at
 * @property integer $updated_at
 */
class Share extends \yii\db\ActiveRecord
{
    public function init()
    {
        if (empty($this->shareKey)) {
            $this->shareKey = time().rand(1,10);
        }
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'share';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['shareKey', 'title', 'description', 'imgUrl'], 'required', 'whenClient' => "function(attribute, value){
                return $('#adv-canshare').attr('checked') == 'checked' && $('#shebei').val() == 0;
            }"],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['shareKey'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 200],
            [['imgUrl'], 'string', 'max' => 100],
            ['shareKey', 'unique'],
            ['imgUrl', 'url'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shareKey' => '分享关键词',
            'title' => '标题',
            'description' => '描述',
            'imgUrl' => '图片地址',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
