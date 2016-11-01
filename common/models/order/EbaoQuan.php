<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ebao_quan".
 *
 * @property integer $id
 * @property integer $type
 * @property string $title
 * @property integer $itemId
 * @property string  $itemType
 * @property integer $uid
 * @property integer $baoId
 * @property string $docHash
 * @property string $preservationTime
 * @property integer $success
 * @property string $errMessage
 */
class EbaoQuan extends \yii\db\ActiveRecord
{
    const TYPE_LOAN = 0;//标的合同
    const TYPE_CREDIT = 1;//债权合同

    const ITEM_TYPE_LOAN_ORDER = 'loan_order';//从标的订单新建的保全
    const ITEM_TYPE_CREDIT_ORDER = 'credit_order';//从债权订单新建的保全,买方
    const ITEM_TYPE_CREDIT_NOTE = 'credit_note';//从结束的转让新建的保全,卖方

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ebao_quan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['itemId', 'uid', 'baoId', 'itemType', 'type'], 'required'],
            [['itemId', 'uid', 'baoId', 'success', 'type', 'created_at', 'updated_at'], 'integer'],
            [['preservationTime'], 'integer', 'max' => 13],
            [['docHash', 'errMessage', 'title'], 'string', 'max' => 200],
            [['itemType'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'itemId' => 'Item Id',
            'itemType' => 'Item Type',
            'uid' => 'Uid',
            'baoId' => 'Bao ID',
            'docHash' => 'Doc Hash',
            'preservationTime' => 'Preservation Time',
            'success' => 'Success',
            'errMessage' => 'Err Message',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
