<?php

namespace common\models\product;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "product_category".
 *
 * @property string $id
 * @property string $name
 * @property string $code
 * @property string $parent_id
 * @property string $status
 * @property string $sort
 * @property string $updated_at
 * @property string $created_at
 */
class ProductCategory extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
    const HOME_STATUS_SHOW = 1;
    const HOME_STATUS_HIDE = 0;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'product_category';
    }
    
    // 这是一对多的关联
    public function getOfflineProducts()
    {
        // 第一个参数为要关联的子表模型类名，
        // 第二个参数指定 通过子表的customer_id，关联主表的id字段
        return $this->hasMany(OfflineProduct::className(), ['category_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '分类ID',
            'name' => '分类名称',
            'code' => '分类编码',
            'parent_id' => '上级分类 ID',
            'status' => '状态',
            'home_status'=>"首页是否显示",
            'description'=>"描述",
            'sort' => 'Sort',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
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
    public static function findProductCategory($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }   
    
    public function createCategory(){
        
        if ($this->validate()) {
            $category = new ProductCategory();
            $now = time();
            $category->name = $this->name;
            $category->sort = $this->sort;
            $category->parent_id = $this->parent_id;
            $category->status = $this->status;
            $category->home_status = $this->home_status;
            $category->updated_at = $now;
            $category->created_at = $now;
            if ($category->save(TRUE,$category)) {
                return $category;
            }
        }
        return NULL;
    }
    
    /**
     * 
     * @param type $status
     * @return type
     */
    public static function findAll($condition=null){
        if(empty($condition)){
            $condition = array('status'=>1);
        }
        return static::findByCondition($condition)->orderBy('parent_id asc,sort desc')->all();
    }
    
   /**
     * 获取分类
     * @return type
     */
    public static function getCategoryTree($condition = array()){
        return  static::_tree(static::findAll($condition));
    }
    
    /**
     * 
     * @staticvar array $tree
     * @param type $list
     * @param type $pid
     * @param type $level
     * @param type $html
     * @return string
     */
    private static function _tree($list, $pid=0, $level=0, $html='——'){
        static $tree = array();
        foreach($list as $v){            
            if($v->parent_id == $pid){
                $tree[$v->id]["name"] = str_repeat($html, $level).$v->name;
                $tree[$v->id]['code'] = $v->code;  
                $tree[$v->id]["front_name"] = $v->name;
                $tree[$v->id]["parent_id"] = $v->parent_id;
                $tree[$v->id]["img_pre"] = "/images/category/";
                static::_tree($list, $v->id, $level+1);
            } 
        }
        return $tree;
    }


}