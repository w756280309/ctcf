<?php

namespace common\models\bank;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\user\User;
use common\utils\SecurityUtils;

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
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class BankCardUpdate extends ActiveRecord implements \P2pl\QpayBindInterface
{
    const STATUS_PENDING = 0;   //已申请
    const STATUS_ACCEPT = 3; //已受理
    const STATUS_SUCCESS = 1;//处理成功
    const STATUS_FAIL = 2;//处理失败

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bank_card_update';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
            'status' => '状态',
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

    /**
     * 获取用户信息.
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }

    public function getTxSn()
    {
        return $this->sn;
    }

    public function getTxDate()
    {
        return date('Ymd', $this->created_at);
    }

    public function getLegalName()
    {
        return $this->cardHolder;
    }

    public function getIdType()
    {
        return 'IDENTITY_CARD';
    }

    public function getIdcard()
    {
        return SecurityUtils::decrypt($this->user->safeIdCard);
    }

    public function getEpayUserId()
    {
        return $this->epayUserId;
    }

    public function getCardNo()
    {
        return $this->cardNo;
    }
}
