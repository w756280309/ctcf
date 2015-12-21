<?php

namespace common\models\order;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "offline_order".
 *
 * @property string $id
 * @property string $user_id
 * @property string $product_sn
 * @property string $product_title
 * @property string $real_name
 * @property string $idcard
 * @property string $org_name
 * @property string $org_code
 * @property string $order_money
 * @property string $yield_rate
 * @property string $product_duration
 * @property string $order_time
 * @property string $pay_time
 * @property integer $status
 * @property integer $toubiao_type
 * @property string $creator_id
 * @property string $updated_at
 * @property string $created_at
 */
class OfflineOrder extends ActiveRecord
{
    //投标状态 0-投标失败 1-成功 2-撤标 3-冻结 4-删除
    const STATUS_FAIL = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_CANCEL = 2;
    const STATUS_FREEZE = 3;
    const STATUS_DEL = 4;
    
    //1、手动 2、自动
    const TOUBIAO_TYPE_SHOUDONG = 1;
    const TOUBIAO_TYPE_ZIDONG = 2;
    
    
    public static function getStatusList(){
        return array(
            self::STATUS_FAIL => '投标失败',
            self::STATUS_SUCCESS => '成功',
            self::STATUS_CANCEL =>  '撤标',
            self::STATUS_FREEZE =>  '冻结',
            self::STATUS_DEL =>  '删除'
        );
    }
    
    public static function getToubiaoTypeList(){
        return array(
            self::TOUBIAO_TYPE_SHOUDONG => '手动',
            self::TOUBIAO_TYPE_ZIDONG => '自动'
        );
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'offline_order';
    }

    public function scenarios() {
        return [
            'update' => ['id', 'product_title', 'real_name', 'idcard', 'order_money', 'yield_rate', 'product_duration', 'order_time','pay_time','org_name', 'org_code','contract_sn'],
            'create' => ['user_id', 'product_sn', 'product_title', 'real_name', 'idcard', 'order_money', 'yield_rate', 'product_duration', 'order_time','pay_time','creator_id','org_name', 'org_code','contract_sn'],
            'create_quote'=>['user_id', 'product_sn', 'product_title', 'real_name', 'idcard', 'order_money', 'order_time'],
            'deposit_return'=>['deposit_return_status','deposit_money'],
            'special_edit'=>['deal_status','deposit_status','contract_sn','deposit_money'],
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
    public function rules()
    {
        return [
            [['product_title', 'order_money', 'yield_rate', 'product_duration', 'order_time','pay_time','contract_sn'], 'required', 'on' => ['create', 'update']],
            [['user_id', 'product_sn'], 'required', 'on' => ['create']],
            
            [['org_name', 'org_code'], 'default','value'=>"", 'on' => ['update']],
            
            ['status', 'default', 'value' => self::STATUS_SUCCESS, 'on' => ['create']],
            ['toubiao_type', 'default', 'value' => self::TOUBIAO_TYPE_SHOUDONG, 'on' => ['create']],
            ['order_money', 'required','on'=>'create_quote'],
            [['order_money'],'match','pattern'=>'/^^[0-9]+(.[0-9]{2})?$/','message'=>'报价格式错误','on'=>'create_quote'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'product_sn' => '标的编号',
            'product_title' => '标的名称',
            'real_name' => '真实姓名',
            'idcard' => '身份证号',
            'org_name' => '投资机构名称',
            'org_code' => '投资机构营业执照编号',
            'order_money' => '认购金额',
            'yield_rate' => '年化收益利率',
            'product_duration' => '项目期限',
            'order_time' => '认购时间',
            'pay_time' => '到期兑付日',
            'contract_sn' => '合同编号',
            'status' => '投标状态',
            'toubiao_type' => '手动/自动',
            'creator_id' => '创建者管理员id',
            'updated_at' => '更新时间',
            'created_at' => '添加时间',
        ];
    }
}
