<?php

namespace common\models\user;

/**
 * 用户绑卡申请模型类
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class QpayBinding extends UserBanks implements \P2pl\QpayBindInterface
{

    const STATUS_INIT = 0;//未申请
    const STATUS_ACK = 3;//处理中，
    const STATUS_SUCCESS = 1;//绑定成功
    const STATUS_FAIL = 2;//绑定失败
    
    public static function tableName()
    {
        return 'qpaybinding';
    }

    public function scenarios()
    {
        return [
            'default' => ['uid', 'epayUserId', 'bank_id', 'account', 'card_number', 'account_type', 'sms', 'bank_name', 'mobile', 'binding_sn'],
        ];
    }

    public function rules()
    {
        return [
            [['uid', 'bank_id', 'account', 'card_number', 'account_type'], 'required'],
            [['card_number'], 'YiiPlus\Validator\CnCardNoValidator'],
            ['mobile', 'match', 'pattern' => '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/', 'message' => '手机号格式错误'],
            [['uid', 'account_type'], 'integer'],
            [['bank_id', 'bank_name', 'sub_bank_name'], 'string', 'max' => 255],
            [['province', 'city', 'account'], 'string', 'max' => 30],
            [['epayUserId', 'status'], 'default', 'value' => 0],
        ];
    }

    /**
     * 获取用户信息.
     *
     * @return UserBanks
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'uid']);
    }
    
    public function getTxSn()
    {
        return $this->binding_sn;
    }

    public function getTxDate()
    {
        return date('Ymd', $this->created_at);
    }

    public function getLegalName()
    {
        return $this->user->real_name;
    }

    public function getIdType()
    {
        return 'IDENTITY_CARD';
    }

    public function getIdNo()
    {
        return $this->user->idcard;
    }

    public function getEpayUserId()
    {
        return $this->epayUserId;
    }

    public function getCardNo()
    {
        return $this->card_number;
    }

}
