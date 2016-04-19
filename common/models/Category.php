<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "category".
 *
 * @property string $id
 * @property string $name
 * @property string $parent
 * @property string $description
 * @property string $sort
 * @property integer $status
 * @property integer $type
 * @property integer $updated_at
 * @property integer $created_at
 */
class Category extends \yii\db\ActiveRecord
{
    const STATUS_HIDDEN = 0;//禁用
    const STATUS_ACTIVE = 1;//可用

    const TYPE_ARTICLE = 1;//分类类型，文章分类

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent', 'sort', 'status', 'type', 'updated_at', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 128],
            ['status', 'default', 'value' => Category::STATUS_ACTIVE],
            ['type', 'default', 'value' => Category::TYPE_ARTICLE],
            ['parent', 'default', 'value' => 0],
            ['sort', 'default', 'value' => 1],
            [['name', 'description'], 'filter', 'filter' => function ($value) {
                return htmlspecialchars($value);
            }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '分类名称',
            'parent' => '上级分类id',
            'description' => '分类描述',
            'sort' => '分类序号',
            'status' => '分类状态',
            'type' => '分类类型',
            'updated_at' => '新建时间',
            'created_at' => '更新时间',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * 获取执行类型分类的分类树，默认获取五级，保持分类顺序
     * @param int $level 分类等级
     * @param int $type 分类类型
     * @return array        Category对象数组
     */
    public static function getTree($level = 5, $type = self::TYPE_ARTICLE)
    {
        //todo 获取分类树
        return [self::findOne(['id' => 1]), self::findOne(['id' => 2])];
    }

    //获取指定节点的子类，默认获取3级，保持循序
    public static function getChildren(Category $node, $level = 3)
    {
        //todo 获取指定节点的子节点
    }

    //获取指定类型的所有分类,不要顺序
    public static function getAllCategories($type = self::TYPE_ARTICLE)
    {
        return self::find()->where(['type' => $type])->all();
    }

}
