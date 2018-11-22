<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_freepwd_record".
 *
 * @property integer $id
 * @property integer $uid
 * @property integer $status
 * @property string $epayUserId
 * @property string $ret_code
 * @property string $ret_msg
 * @property string $remark
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserFreepwdRecord extends \yii\db\ActiveRecord
{
    const OPEN_FREE_STATUS_WAIT = 0;//未处理
    const OPEN_FASTPAY_STATUS_UNPASS = 1;//快捷支付开通失败
    const OPEN_FASTPAY_STATUS_PASS = 2;//快捷支付开通成功
    const OPEN_FREE_RECHARGE_UNPASS = 3;//快捷免密充值开通失败
    const OPEN_FREE_RECHARGE_PASS = 4;//快捷免密充值开通成功
    /**
     * @inheritdoc
     */
    const STATUS_UN_DEAL = 0; //0-未处理 1-快捷支付开通失败 2-快捷支付开通成功 3-快捷免密充值开通失败 4-快捷免密开通成功'
    const STATUS_FAIL = 1;
    const STATUS_SUCCESS = 2;
    const SETTLE_NO_PASSWORD_FAIL = 3;
    const SETTLE_NO_PASSWORD_SUCCESS = 4;

    public static function tableName()
    {
        return 'user_freepwd_record';
    }

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
            [['uid', 'created_at', 'updated_at'], 'integer'],
            [['epayUserId', 'ret_code', 'ret_msg'], 'required'],
            [['status'], 'string', 'max' => 2],
            [['epayUserId', 'ret_code'], 'string', 'max' => 60],
            [['ret_msg'], 'string', 'max' => 200],
            [['remark'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => '用户id',
            'status' => '状态',
            'epayUserId' => '联动用户账户号',
            'ret_code' => '返回信息状态码',
            'ret_msg' => '返回信息',
            'remark' => '备注',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    public static function getStatusInfo()
    {
        return [
            self::OPEN_FREE_STATUS_WAIT,
            self::OPEN_FASTPAY_STATUS_UNPASS,
            self::OPEN_FASTPAY_STATUS_PASS,
            self::OPEN_FREE_RECHARGE_UNPASS,
            self::OPEN_FREE_RECHARGE_PASS,
        ];
    }

    public static function getCheckStatusInfo()
    {
        return [
            self::OPEN_FREE_STATUS_WAIT=>['code'=>self::OPEN_FREE_STATUS_WAIT, 'tourl' => '/user/userbank/fastpay', 'message'=>'未开通快捷支付(商业委托)'],
            self::OPEN_FASTPAY_STATUS_UNPASS=>['code'=>self::OPEN_FASTPAY_STATUS_UNPASS, 'tourl' => '/user/userbank/fastpay','message'=>'开通快捷支付(商业委托)失败, 继续开通'],
            self::OPEN_FASTPAY_STATUS_PASS=>['code'=>self::OPEN_FASTPAY_STATUS_PASS,'tourl' => '/user/userbank/free-recharge','message'=>'已开通快捷支付(商业委托)，继续开通免密充值(商业委托)'],
            self::OPEN_FREE_RECHARGE_UNPASS=>['code'=>self::OPEN_FREE_RECHARGE_UNPASS,'tourl' => '/user/userbank/free-recharge','message'=>'开通免密充值(商业委托)失败，继续开通'],
            self::OPEN_FREE_RECHARGE_PASS=>['code'=>self::OPEN_FREE_RECHARGE_PASS,'tourl' => '','message'=>'开通免密充值(商业委托)成功'],
        ];
    }

    public function getTxDate()
    {
        return date('Ymd', $this->created_at);
    }
}
