<?php

namespace common\models\news;

use common\models\category\Category;
use common\models\category\ItemCategory;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "news".
 *
 * @property string  $id
 * @property string  $title
 * @property string  $summary
 * @property string  $image
 * @property string  $source
 * @property string  $creator_id
 * @property string  $status
 * @property integer $home_status
 * @property string  $sort
 * @property string  $body
 * @property integer $news_time
 * @property integer $updated_at
 * @property integer $created_at
 * @property boolean $allowShowInList 是否在列表中显示
 */
class News extends ActiveRecord
{
    const STATUS_NO_PUBLISH = 0;    //未上线
    const STATUS_PUBLISH = 1;   //上线
    const STATUS_DELETE = 3;    //删除

    const CATEGORY_TYPE_ARTICLE = 1;//分类类型，文章分类

    public $category;

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
            [['title', 'body', 'news_time', 'status', 'creator_id', 'category'], 'required'],
            [['status', 'home_status', 'sort'], 'integer'],
            [['body'], 'string'],
            [['title', 'source', 'child_title'], 'string', 'max' => 100],
            [['image'], 'string', 'max' => 250],
            [['summary'], 'string', 'max' => 200],
            ['pc_thumb', 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg'],
        ];
    }

    public static function initNew()
    {
        $model = new self();
        $model->sort = 0;
        $model->category = [];
        $model->source = '';
        $model->pc_thumb = '';
        return $model;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '新闻标题',
            'child_title' => '新闻副标题',
            'image' => '内容图片',
            'source' => '内容来源',
            'creator_id' => '创建者管理员id',
            'status' => '状态',
            'home_status' => '是否在首页显示',
            'sort' => '序号',
            'body' => '新闻内容',
            'news_time' => '新闻发布时间',
            'pc_thumb' => 'PC缩略图',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!is_integer($this->news_time)) {
                $this->news_time = strtotime($this->news_time);
            }
            //保存之前，清空原有分类
            ItemCategory::clearItems([$this->id], News::CATEGORY_TYPE_ARTICLE);
            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (count($this->category) > 0) {
            //保存之后添加分类
            foreach ($this->category as $id) {
                $category = Category::find()->where(['id' => $id, 'type' => Category::STATUS_ACTIVE])->one();
                if ($category) {
                    ItemCategory::addItem($this->id, $category);
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public static function getStatusList()
    {
        return array(
            self::STATUS_NO_PUBLISH => "草稿",
            self::STATUS_PUBLISH => "发布",
            self::STATUS_DELETE => "删除",
        );
    }


    public function getItemCategories()
    {
        $item_category = ItemCategory::find()->where(['item_id' => $this->id, 'type' => self::CATEGORY_TYPE_ARTICLE])->all();
        return $item_category;
    }

    public function getCategories()
    {
        $item_category = $this->getItemCategories();
        if ($item_category) {
            return Category::find()->where(['in', 'id', ArrayHelper::getColumn($item_category, 'category_id')])->andWhere(['type' => self::CATEGORY_TYPE_ARTICLE, 'status' => Category::STATUS_ACTIVE])->all();
        }
        return [];
    }
}
