<?php

namespace common\models\channel;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "channel_order".
 *
 * @property integer $id
 * @property string $order_sn
 * @property string $product_sn
 * @property string $channel_product_sn
 * @property string $channel_product_title
 * @property string $channel_order_sn
 * @property string $channel_user_sn
 * @property string $channel_yield_rate
 * @property string $channel_order_money
 * @property integer $channel_order_time
 * @property integer $channel_order_days
 * @property integer $channel_finish_days
 * @property integer $channel_continue_days
 * @property integer $created_at
 * @property integer $updated_at
 */
class ChannelOrder extends \yii\db\ActiveRecord
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
        return 'channel_order';
    }

    public static function createSn($pre="JDDO"){
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float)$usec + (float)$sec);
        
        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999),$usec);
        return $pre.str_replace('x', $sec, $date);
    }
    
    

    /** 格式化时间戳，精确到毫秒，x代表毫秒 */
    public function microtime_format($tag, $time)
    {
       list($usec, $sec) = explode(".", $time);
       $date = date($tag,$usec);
       return str_replace('x', $sec, $date);
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
            [['order_sn', 'product_sn', 'channel_product_sn', 'channel_order_sn', 'channel_user_sn'], 'required'],
            [['channel_yield_rate', 'channel_order_money'], 'number'],
            [[ 'channel_order_days', 'channel_finish_days', 'channel_continue_days', 'created_at', 'updated_at'], 'integer'],
            [['order_sn', 'product_sn', 'channel_product_sn', 'channel_product_title', 'channel_order_sn', 'channel_user_sn'], 'string', 'max' => 30],
            ['remark','string'],
            //[['channel_order_time','start_time','end_time'],'match','pattern'=>'/^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}\s\d{1,2}:\d{1,2}:\d{1,2}$/','message'=>'日期格式不正确'],
            [['channel_order_money','channel_yield_rate'],'match','pattern'=>'/^[0-9]+(\.[0-9]+)?$/','message'=>'格式不正确'],//
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => 'Order Sn',
            'product_sn' => 'Product Sn',
            'channel_product_sn' => 'Channel Product Sn',
            'channel_product_title' => 'Channel Product Title',
            'channel_order_sn' => 'Channel Order Sn',
            'channel_user_sn' => 'Channel User Sn',
            'channel_yield_rate' => 'Channel Yield Rate',
            'channel_order_money' => 'Channel Order Money',
            'channel_order_time' => 'Channel Order Time',
            'channel_order_days' => 'Channel Order Days',
            'channel_finish_days' => 'Channel Finish Days',
            'channel_continue_days' => 'Channel Continue Days',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
