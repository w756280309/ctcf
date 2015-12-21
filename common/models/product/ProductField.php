<?php

namespace common\models\product;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Description of OfflineProduct
 *
 * @author zhy-pc
 */
class ProductField extends ActiveRecord {

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    
    const PRO_TYPW_OFFLINE = 1;
    const PRO_TYPW_ONLINE = 2;
    
    const FIELD_TYPW_OFFLINE = 1;
    const FIELD_TYPW_ONLINE = 2;
    
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'product_field';
    }

    public function scenarios() {
        return [
            'update' => ['id','product_id', 'name'],
            'create' => [ 'product_id', 'name', 'content'],
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
            [['id'], 'required', 'on' => ['update']],
            [['product_type', 'field_type', 'product_id'], 'required', 'on' => ['create', 'update']],
            ['status', 'default', 'value' => self::STATUS_ACTIVE, 'on' => ['create', 'update']],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED], 'on' => ['create', 'update']],
            [['status'], 'integer', 'on' => ['create', 'update']],
            ['field_type', 'default', 'value' => self::FIELD_TYPW_OFFLINE, 'on' => ['create', 'update']],
            ['product_type', 'default', 'value' => self::PRO_TYPW_OFFLINE, 'on' => ['create', 'update']],
        ];
    }

}
