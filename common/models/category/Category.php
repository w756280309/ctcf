<?php

namespace common\models\category;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "category".
 *
 * @property string $id
 * @property string $name
 * @property string $key
 * @property string $parent_id
 * @property string $level
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

    public static function tableName()
    {
        return 'category';
    }

    public function rules()
    {
        return [
            [['name', 'status', 'key'], 'required'],
            [['parent_id', 'sort', 'status', 'type', 'updated_at', 'created_at', 'level'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['key'], 'string', 'max' => 20],
            [['description'], 'string', 'max' => 128],
            ['key','match','pattern'=>'/^[a-zA-Z_]{1,20}$/'],
            ['parent_id', 'compare', 'compareAttribute' => 'id', 'operator' => '!='],
        ];
    }

    /**
     * 初始化对象
     * @return Category
     */
    public static function initNew()
    {
        $model = new self();
        $model->status = Category::STATUS_ACTIVE;
        $model->parent_id = 0;
        $model->level = 1;
        $model->sort = 1;
        return $model;
    }

    /**
     * @param int $id 对象id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public static function findOrNew($id)
    {
        if (!empty($id)) {
            if (($model = self::findOne($id)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('指定对象没有找到');
            }
        } else {
            $model = self::initNew();
            return $model;
        }
    }

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
     * 插入数据前计算分类层级
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->parent_id) {
                $this->parent_id = 0;
            }
            if ($this->parent) {
                $this->level = $this->parent->level + 1;
            } else {
                $this->level = 1;
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取父类关联对象
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    /**
     * 获取指定节点的子类(或全部节点)，默认最高层级为3级，保持分类父子关系
     * @param integer $type 分类类型
     * @param int $level 子类最高层级
     * @param Category $node 分类对象
     * @return array    排好序的分类对象数组
     */
    public static function getTree($type, $level = 3, Category $node = null)
    {
        $list = self::getAllCategories($node ? $node['type'] : $type, $level);
        return self::_tree($list, $node ? $node['id'] : 0, $level);
    }

    /**
     * 获取指定类型的所有分类
     * @param int $type 分类类型
     * @param int $level 最高层级
     * @return array|\yii\db\ActiveRecord[] 分类对象数组
     */
    private static function getAllCategories($type, $level = 3)
    {
        $query = self::find()->where(['status' => self::STATUS_ACTIVE, 'type' => $type])->andWhere(['<=', 'level', $level]);
        return $query->orderBy(['parent_id' => SORT_ASC, 'sort' => SORT_DESC])->all();
    }

    /**
     * 获取指定数组的层级关系
     * @param array $list 需要排序的分类数组
     * @param int $pid 顶层分类的父类id
     * @param int $level 子类最高层级
     * @return array        分类对象数组
     */
    private static function _tree(array $list, $pid = 0, $level = 3)
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
        }
        return null;
    }

    /**
     * 获取分类数组
     * @return array
     */
    public static function getTypeArray()
    {
        $types = Yii::$app->params['category_type'];
        return $types ?: [];
    }


    public static function getStatusArray()
    {
        return [self::STATUS_ACTIVE => '可用', self::STATUS_HIDDEN => '禁用'];
    }

}
