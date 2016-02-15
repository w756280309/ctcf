<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\service\SmsService;

/**
 * This is the model class for table "user_bank".
 *
 * @property string $id
 * @property string $uid
 * @property string $bank_id
 * @property string $bank_name
 * @property string $sub_bank_name
 * @property string $province
 * @property string $city
 * @property string $account
 * @property string $card_number
 * @property integer $account_type
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class UserBanks extends \yii\db\ActiveRecord
{

    const PERSONAL_ACCOUNT = 11;
    const COMPANY_ACCOUNT = 12;

    public $sms;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_bank';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function scenarios()
    {
        return [
            'step_first' => ['uid', 'bank_id', 'account', 'card_number', 'account_type', 'sms', 'bank_name', 'mobile', 'binding_sn'],
            'step_second' => ['sub_bank_name', 'province', 'city'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uid', 'bank_id', 'account', 'card_number', 'account_type', 'mobile'], 'required', 'on' => 'step_first'],
            [['card_number'], 'checkCardNumber', 'on' => 'step_first'],
            [['card_number'], 'unique', 'message' => '该银行卡号已被占用', 'on' => 'step_first'],
            ['mobile', 'match', 'pattern' => '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/', 'message' => '手机号格式错误', 'on' => 'step_first'],
            [['sub_bank_name', 'province', 'city'], 'required', 'on' => 'step_second'],
            [['uid', 'account_type'], 'integer'],
            [['bank_id', 'bank_name', 'sub_bank_name'], 'string', 'max' => 255],
            [['province', 'city', 'account'], 'string', 'max' => 30],
            ['epayUserId', 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'bank_id' => '银行',
            'bank_name' => '银行',
            'sub_bank_name' => '分支行名称',
            'province' => '分支行所在省份',
            'city' => '分支行所在城市',
            'account' => '持卡人姓名',
            'card_number' => '银行卡号',
            'sms' => '短信码',
            'account_type' => 'Account Type',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 验证身份证
     */
    public function checkCardNumber($attribute, $params)
    {
        $str_card = $this->$attribute;
        $str_card_len = strlen($str_card);
        if ($str_card_len < 16 || $str_card_len > 19) {
            $this->addError($attribute, "你输入的银行卡号有误");
        } else {
            return TRUE;
        }
    }

}
