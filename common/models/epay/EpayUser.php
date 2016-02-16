<?php

namespace common\models\epay;

/**
 * This is the model class for table "EpayUser".
 *
 * @property int $id
 * @property string $appUserId
 * @property int $epayId
 * @property string $epayUserId
 * @property string $accountNo
 * @property string $regDate
 * @property int $clientIp
 * @property string $createTime
 */
class EpayUser extends \yii\db\ActiveRecord
{
    use \YiiPlus\Model\ErrorExTrait;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'EpayUser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['appUserId', 'epayUserId', 'regDate', 'clientIp', 'createTime'], 'required'],
            [['epayId', 'clientIp'], 'integer'],
            [['regDate', 'createTime'], 'safe'],
            [['appUserId', 'epayUserId', 'accountNo'], 'string', 'max' => 60],
            ['epayId', 'default', 'value' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'appUserId' => '应用方用户ID（兼容非数字的用户标识）',
            'epayId' => '托管方ID',
            'epayUserId' => '托管用户ID',
            'accountNo' => '托管账户号',
            'regDate' => '开户日期',
            'clientIp' => 'IP',
            'createTime' => '记录时间',
        ];
    }
}
