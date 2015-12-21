<?php

namespace common\models\news;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\data\Pagination;

/**
 * This is the model class for table "news_category".
 *
 * @property string $id
 * @property string $name
 * @property string $parent
 * @property string $description
 * @property string $sort
 * @property integer $status
 * @property integer $updated_at
 * @property integer $created_at
 */
class NewsCategory extends \yii\db\ActiveRecord
{
    private $_html;
    
    public function getHtml(){
        return $this->_html;
    }
    public function setHtml($value){
        $this->_html = $value;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news_category';
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
            [['name', 'parent', 'sort', 'status'], 'required'],
            [['parent', 'sort', 'status'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 128],
            ['status', 'default', 'value' => 1]
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
            'parent' => '上级分类',
            'description' => '描述',
            'sort' => '分类顺序',
            'status' => '分类状态 ',
            'updated_at' => '更新时间',
            'created_at' => '创建时间',
        ];
    }
    
    
    /**
     * 
     * @param type $status
     * @return type
     */
    public static function findAll($status=null){
        $_status = $status ? $status : 1;
        return static::findByCondition(['status'=>$_status])->orderBy('parent asc')->all();
    }
    
    /**
     * 获取子分类
     * 
     * @param type $parent
     * @return type
     */
    public static function findByParent($parent=0){
        return static::findByCondition(['parent'=>$parent])->orderBy('id asc')->all();
    }
    
    /**
     * 
     * @param type $id
     * @return type
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * 获取新闻分类
     * @return type
     */
    public static function getCategoryTree(){
        return  static::_tree(static::findAll());
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
            if($v->parent == $pid){
                $tree[$v->id] = str_repeat($html, $level).$v->name;  
                static::_tree($list, $v->id, $level+1);
            } 
        }
        return $tree;
    }
    
    public static function getCategoryList(){
        return  static::_treeList(static::find()->all());
    }
    
    private static function _treeList($list, $pid=0, $level=0, $html='——'){
        static $tree = array();
        foreach($list as $v){            
            if($v->parent == $pid){
                $tree[] = ['id'=>$v->id, 'name'=>str_repeat($html, $level).$v->name, 'sort'=>$v->sort, 'status'=>$v->status];
                static::_treeList($list, $v->id, $level+1);
            } 
        }
        return $tree;
    }
    
}
