<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

class BatchpayItem extends \yii\db\ActiveRecord {

    //账户类型： 11=个人账户 12=企业账户
    const ACCOUNT_TYPE_PERSONAL = 11;
    const ACCOUNT_TYPE_AGENCY = 12;
    
    //交易状态 10=未处理 20=正在处理 30=代付成功 40=代付失败
    const STATUS_WAIT = 10;
    const STATUS_DOING = 20;
    const STATUS_SUCCESS = 30;
    const STATUS_FAIL = 40;
    
    //开户证件类型 0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证 X=其他证件
    const STATUS_ID = 0;

    /**
     * 定义表名
     */
    public static function tableName() {
        return 'batchpay_item';
    }

    /**
     * 定义验证规则
     */
    public function rules() {
        return [
            [['batchpay_id', 'draw_id', 'amount', 'uid', 'account_id', 'bank_id', 'pay_bank_id', 'account_type', 'account_name', 'account_number', 'branch_name', 'province', 'city'], 'required'],
            ['identification_type', 'default', 'value' => 0],
            ['account_type', 'default', 'value' => self::ACCOUNT_TYPE_PERSONAL]
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 字段名
     */
    public function attributeLabels() {
        return [
        ];
    }

    /**
     *
     * 返回 draw_record
     */
    public function getDraw() {
        return $this->hasOne(DrawRecord::className(), ['id' => 'draw_id']);
    }
}
