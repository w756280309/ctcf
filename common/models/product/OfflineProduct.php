<?php

namespace common\models\product;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Description of OfflineProduct
 *
 * @author zhy-pc
 */
class OfflineProduct extends ActiveRecord {

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
    const DEL_STATUS_SHOW = 0;
    const DEL_STATUS_DEL = 1;
    
    const HOME_STATUS_HIDDEN = 0;
    const HOME_STATUS_SHOW = 1; 
    
    //标的进展： 即将开始 挂牌公告 协议签署 项目成交 
    const PRODUCT_STATUS_YUGAO = 1;
    const PRODUCT_STATUS_GUAPAI = 2;
    const PRODUCT_STATUS_QIANSHU = 3;
    const PRODUCT_STATUS_CHENGJIAO = 4;
    
    const PRODUCT_STATUS_SPECIAL_YUGAO = 11;
    const PRODUCT_STATUS_SPECIAL_MEANS = 12;
    const PRODUCT_STATUS_SPECIAL_JIAOGE = 13;

    const DURATION_TYPE_DAY=0;
    const DURATION_TYPE_MONTH=1;
    const DURATION_TYPE_YEAR=2;

    public $cat_code;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'offline_product';
    }

    public function scenarios() {
        return [
            'line' => ['status'],
            'del' => ['del_status'],
            'update' => ['id', 'sn','title', 'category_id', 'money', 'yield_rate', 'start_money', 'product_duration','product_duration_type','product_status', 'description','home_status','account_name','account','bank','del_status'],
            'create' => ['title', 'sn', 'category_id', 'money', 'yield_rate', 'start_money', 'product_duration','product_duration_type','product_status', 'description','home_status', 'creator_id','account_name','account','bank'],
            'create_special' => ['title', 'sn', 'category_id', 'money','home_status','end_time','special_type_title','contact','contact_mobile','description', 'creator_id','product_status'],
            'line'=>['status'],
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
    public function rules() {
        return [
           // ['sn','unique','message'=>'编号已占用'],
            [['id'], 'required', 'on' => ['update']],
            [['id','status'], 'required', 'on' => ['line']],
            [['id','del_status'], 'required', 'on' => ['del']],
           // [['account_name','account','bank'], 'required', 'on' => ['create']],
            [['title', 'sn', 'category_id', 'money', 'yield_rate', 'start_money', 'product_duration', 'description', 'creator_id'], 'required', 'on' => ['create', 'update']],
            ['status', 'default', 'value' => self::STATUS_ACTIVE, 'on' => ['create', 'update']],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED], 'on' => ['create', 'update']],
            ['del_status', 'default', 'value' => self::DEL_STATUS_SHOW, 'on' => ['create']],
            ['del_status', 'in', 'range' => [self::DEL_STATUS_SHOW, self::DEL_STATUS_DEL], 'on' => ['create', 'update']],
            [['status'], 'integer', 'on' => ['create', 'update']],
            ['product_duration_type', 'default', 'value' => self::DURATION_TYPE_DAY, 'on' => ['create', 'update']],
            [['product_duration'], 'integer', 'on' => ['create', 'update']],
            [['money','start_money'], 'double', 'on' => ['create', 'update']],
            ['product_status', 'default', 'value' => self::PRODUCT_STATUS_YUGAO, 'on' => ['create', 'update']],
           // ['product_status', 'in', 'range' => [self::PRODUCT_STATUS_YUGAO, self::PRODUCT_STATUS_CHENGJIAO], 'on' =>['create', 'update']],
            [['product_status'], 'integer', 'on' => ['create', 'update']],
            ['home_status', 'default', 'value' => self::HOME_STATUS_SHOW, 'on' => ['create', 'update']],
            [['category_id'], 'integer', 'on' => ['create', 'update']],
            [['title', 'sn', 'category_id', 'money','home_status','end_time','special_type_title','contact','contact_mobile', 'creator_id'], 'required', 'on' => ['create_special']],
            //[['special_type'], 'in', 'range' => array(1, 2),'message'=>"请选择类型", 'on' => ['create_special']],
             ['contact_mobile','match','pattern'=>'/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/','message'=>'手机号格式错误', 'on' => ['create_special']],
        ];
    }

    // 获取分类
    public function getProductCategory()
    {
        //同样第一个参数指定关联的子表模型类名
        //
        return $this->hasOne(ProductCategory::className(), ['id' => 'category_id']);
    }
    
    public static function durationTypeList(){
        return array(
            self::DURATION_TYPE_DAY=>"天",
            self::DURATION_TYPE_MONTH=>"个月",
           // self::DURATION_TYPE_YEAR=>"年"
        );
    }

    public static function getStatusList(){
        return array(
            self::STATUS_DELETED=>"无效不显示",
            self::STATUS_ACTIVE=>"有效显示",
        );
    }

    public static function getSpecialType($key = null){
        $data = array(
           0 => '请选择',
           1 => '房产',
           2 => '不良资产'
        );
        if(!empty($key)){
            return $data[$key];
        }
        return $data;
    }


    public static function getProductStatusAll($key = null){
        $data = array(
            self::PRODUCT_STATUS_YUGAO => '即将开始',
            self::PRODUCT_STATUS_GUAPAI => '挂牌公告',
            self::PRODUCT_STATUS_QIANSHU => '协议签署',
            self::PRODUCT_STATUS_CHENGJIAO => '项目成交',
            self::PRODUCT_STATUS_SPECIAL_YUGAO => '挂牌公告',
            self::PRODUCT_STATUS_SPECIAL_MEANS => '意向成交',
            self::PRODUCT_STATUS_SPECIAL_JIAOGE => '交割完成',
        );
        if(!empty($key)){
            return $data[$key];
        }
        return $data;
    }


    public static function getProductStatusList($key = null){
        $data = array(
            self::PRODUCT_STATUS_YUGAO => '即将开始',
            self::PRODUCT_STATUS_GUAPAI => '挂牌公告',
            self::PRODUCT_STATUS_QIANSHU => '协议签署',
            self::PRODUCT_STATUS_CHENGJIAO => '项目成交'
        );
        if(!empty($key)){
            return $data[$key];
        }
        return $data;
    }
    
    public static function getProductSpecialStatusList($key = null){
        $data = array(
            self::PRODUCT_STATUS_SPECIAL_YUGAO => '挂牌公告',
            self::PRODUCT_STATUS_SPECIAL_MEANS => '意向成交',
            self::PRODUCT_STATUS_SPECIAL_JIAOGE => '交割完成',
        );
        if(!empty($key)){
            return $data[$key];
        }
        return $data;
    }
    

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => '项目名称',
            'sn' => '项目编号',
            'status' => '显示状态',
            'category_id' => '分类ID',
            'money' => '项目融资总额',
            'yield_rate' => '年化收益利率',
            'start_money' => '起投金额',
            'account_name'=>"账户名称",
            'account'=>"账户",
            'bank'=>"开户行",
            'product_duration' => '项目期限',
            'product_status' => '项目状态',
            'home_status'=> '是否首页展示',
            'description' => '项目其它描述',
            'creator_id' => '创建者管理员id',
            'updated_at' => '创建时间',
            'created_at' => '更新时间',
            'special_type'=>"特殊资产类型",
            'special_type_title'=>'特殊资产类型',
            'contact'=>"联系人",
            'contact_mobile'=>"联系人手机号",
            'end_time'=>'结束时间'
        ];
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findBySN($sn,$id=0) {
        $pro = static::find()->where(['sn' => $sn, 'status' => self::STATUS_ACTIVE]);
        if($id){
            $pro->andFilterWhere(['<>','id',$id]);
        }
        return $pro->one();
    }

}
