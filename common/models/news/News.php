<?php

namespace common\models\news;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\data\Pagination;

/**
 * This is the model class for table "news".
 *
 * @property string $id
 * @property string $title
 * @property string $image
 * @property string $source
 * @property string $category_id
 * @property string $creator_id
 * @property string $status
 * @property integer $home_status
 * @property string $sort
 * @property string $body
 * @property integer $news_time
 * @property integer $updated_at
 * @property integer $created_at
 */
class News extends \yii\db\ActiveRecord
{
    const HOME_STATUS_HIDDEN = 0;
    const HOME_STATUS_SHOW = 1;
    
    const STATUS_NO_PUBLISH= 0;
    const STATUS_PUBLISH = 1;
    const STATUS_DELETE = 3;
    
    public static function getStatusList(){
        return array(
            self::STATUS_NO_PUBLISH=>"草稿",
            self::STATUS_PUBLISH=>"发布",
            self::STATUS_DELETE=>"删除",
        );
    }
    
    public static function getHomeStatusList(){
        return array(
            self::HOME_STATUS_HIDDEN => "不显示",
            self::HOME_STATUS_SHOW => "显示",
        );
    }
    
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
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
            [['title', 'body', 'news_time','sort','status','creator_id'], 'required'],
            [[ 'status', 'home_status', 'sort'], 'integer'],            
            [['body'], 'string'],
            [['title', 'source','child_title'], 'string', 'max' => 100],
            [['image', 'attach_file'], 'string', 'max' => 250],
            [['title','body'],'filter','filter'=>function($value){
                return htmlspecialchars($value);
            }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '新闻标题',
            'child_title'=>'新闻副标题',
            'image' => '内容图片',
            'source' => '内容来源',
            'category_id' => '所属分类',
            'creator_id' => '创建者管理员id',
            'status' => '状态',
            'home_status' => '是否在首页显示',
            'sort' => '序号',
            'body' => '新闻内容',
            'news_time' => '新闻发布时间',
            'attach_file' => '上传附件',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }


    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)){
            if(!is_integer($this->news_time)){
                $this->news_time = strtotime($this->news_time);
            }
            return true;
        }else{
            return false;
        }
    }


}
