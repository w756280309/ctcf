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
 * @property integer $orderId
 * @property integer $uid
 * @property integer $baoId
 * @property string $docHash
 * @property string $preservationTime
 * @property integer $success
 * @property string $errMessage
 */
class EbaoQuan extends \yii\db\ActiveRecord
{
    const TYPE_SUBSCRIPTION = 0;//认购协议
    const TYPE_RISK = 1;//风险提示书
    const TYPE_CONTENT = 2;//项目说明

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
            [['orderId', 'uid', 'baoId'], 'required'],
            [['orderId', 'uid', 'baoId', 'success', 'type', 'created_at', 'updated_at'], 'integer'],
            [['preservationTime'], 'integer', 'max' => 13],
            [['docHash', 'errMessage', 'title'], 'string', 'max' => 200]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orderId' => 'Order ID',
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
