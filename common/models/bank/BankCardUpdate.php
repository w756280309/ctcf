<?php

namespace common\models\bank;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "BankCardUpdate".
 *
 * @property string $id
 * @property string $sn
 * @property string $oldSn
 * @property string $uid
 * @property string $epayUserId
 * @property string $bankId
 * @property string $bankName
 * @property string $cardHolder
 * @property string $cardNo
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class BankCardUpdate extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BankCardUpdate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sn', 'oldSn', 'uid', 'epayUserId'], 'required'],
            [['uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['sn', 'oldSn'], 'string', 'max' => 32],
            [['epayUserId'], 'string', 'max' => 60],
            [['bankId', 'bankName'], 'string', 'max' => 255],
            [['cardHolder'], 'string', 'max' => 30],
            [['cardNo'], 'string', 'max' => 50],
            [['sn'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '换卡流水号',
            'oldSn' => '原绑卡流水号',
            'uid' => '用户ID',
            'epayUserId' => '托管平台用户号',
            'bankId' => '银行id',
            'bankName' => '银行名称',
            'cardHolder' => '持卡人姓名',
            'cardNo' => '银行卡号',
            'status' => '状态',//状态 0-已申请 1-处理中 3-已通过
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
}
