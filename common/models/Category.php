<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property string $id
 * @property string $name
 * @property string $parent_id
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
    const TYPE_PRODUCT = 2; //分类类型，标的
    const TYPE_AUTH = 3;//分类类型，权限
    const TYPE_OTHER = 9;//分类类型，其他


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
            [['name', 'parent_id', 'status'], 'required'],
            [['parent_id', 'sort', 'status', 'type', 'updated_at', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 128],
            ['status', 'default', 'value' => Category::STATUS_ACTIVE],
            ['type', 'default', 'value' => Category::TYPE_ARTICLE],
            ['parent_id', 'default', 'value' => 0],
            ['sort', 'default', 'value' => 1],
            [['name', 'description'], 'filter', 'filter' => function ($value) {
                return htmlspecialchars($value);
            }],
            ['parent_id', 'compare', 'compareAttribute' => 'id', 'operator' => '!='],
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
            'parent_id' => '上级分类id',
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
        $list = self::getAllCategories(self::TYPE_ARTICLE);
        return self::_tree($list, 0, 5);
    }

    public static function getDropDownTree($level = 5, $type = self::TYPE_ARTICLE)
    {
        $list = ArrayHelper::map(self::getTree($level, $type), 'id', 'name');
        $return['0'] = '顶级分类';
        if (count($list) > 0) {
            foreach ($list as $key => $value) {
                $return[$key] = $value;
            }
        }
        return $return;
    }

    /**
     * 获取指定节点的子类，默认获取3级，保持循序
     * @param Category $node 分类对象
     * @param int $level 子类最高层级
     * @return array    排好序的分类对象数组
     */
    public static function getChildren(Category $node, $level = 3)
    {
        $list = self::getAllCategories($node['type']);
        return self::_tree($list, $node['id'], $level);
    }

    /**
     * 获取指定类型的所有分类,不要顺序
     * @param int $type 分类类型
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllCategories($type = self::TYPE_ARTICLE)
    {
        return self::find()->where(['status' => self::STATUS_ACTIVE, 'type' => $type])->orderBy(['parent_id' => SORT_ASC, 'sort' => SORT_DESC])->all();
    }

    /**
     * 获取指定数组的层级关系
     * @param array $list 需要排序的分类数组
     * @param int $pid 顶层分类的父类id
     * @param int $level 子类最高层级
     * @return array        分类对象数组
     */
    private static function _tree(array $list, $pid = 0, $level = 5)
    {
        static $tree = [];
        if (count($list) > 0 && $level > 0) {
            foreach ($list as $k => $v) {
                if ($v['parent_id'] == $pid) {
                    $tree[] = $v;
                    unset($list[$k]);
                    static::_tree($list, $v['id'], $level - 1);
                }
            }
        }
        return $tree;
    }


    /**
     * @return string
     */
    public function getTypeName()
    {
        $types = Category::getTypeArray();
        if (in_array($this->type, array_keys($types))) {
            return $types[$this->type];
        } else {
            return '-';
        }
    }

    /**
     * 获取分类数组
     * @return array
     */
    public static function getTypeArray()
    {
        return [self::TYPE_ARTICLE => '文章分类', self::TYPE_PRODUCT => '标的分类', self::TYPE_AUTH => '权限分类', self::TYPE_OTHER => '其他分类'];
    }

    /**
     * 获取父类名称
     * @return string
     */
    public function getParentName()
    {
        if (intval($this->parent_id) === 0) {
            return '顶级分类';
        } else {
            $model = Category::find()->where(['id' => $this->parent_id])->one();
            if ($model) {
                return $model->name;
            } else {
                return '-';
            }
        }
    }

    public static function getStatusArray()
    {
        return [self::STATUS_ACTIVE => '可用', self::STATUS_HIDDEN => '禁用'];
    }

    public function getStatusName()
    {
        $status_array = Category::getStatusArray();
        if (in_array($this->status, array_keys($status_array))) {
            return $status_array[$this->status];
        } else {
            return '-';
        }
    }
}
