<?php

namespace common\models\adv;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "share".
 *
 * @property integer $id            ID
 * @property string  $shareKey      分享KEY
 * @property string  $title         分享标题
 * @property string  $description   分享描述
 * @property string  $imgUrl        分享图片地址
 * @property string  $url           分享链接地址
 * @property integer $created_at    创建时间
 * @property integer $updated_at    修改时间
 */
class Share extends ActiveRecord
{
    public function init()
    {
        if (empty($this->shareKey)) {
            $this->shareKey = time().rand(1, 10);
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
            [['shareKey', 'title', 'description', 'imgUrl', 'url'], 'required', 'whenClient' => "function(attribute, value){
                return $('#adv-canshare').attr('checked') == 'checked' && $('#shebei').val() == 0;
            }"],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['shareKey'], 'string', 'max' => 20],
            [['title'], 'string', 'max' => 200],
            [['imgUrl'], 'string', 'max' => 100],
            ['shareKey', 'unique'],
            [['imgUrl', 'url'], 'url'],
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
            'url' => '分享链接地址',
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
