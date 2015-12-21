<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

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
class UserBank extends \yii\db\ActiveRecord {

	const STATUS_NO = 0;
	const STATUS_YES = 1;
	const STATUS_DENY = 2;
	const STATUS_ING = 3;
	const BIND_COUNT = 3;

        public $sms;
        
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'user_bank';
	}

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
			[['uid', 'bank_id', 'sub_bank_name', 'province', 'city', 'account', 'card_number', 'account_type'], 'required'],
			[['uid', 'account_type', 'status'], 'integer'],
			[['bank_id', 'bank_name', 'sub_bank_name'], 'string', 'max' => 255],
			[['province', 'city', 'account'], 'string', 'max' => 30],
			//验证卡号
			[['card_number'], 'checkCardNumber'],
                        ['sms','string','length'=>6]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
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
			'account_type' => 'Account Type',
			'status' => 'Status',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
		];
	}

	/**
	 * 验证身份证
	 */
	public function checkCardNumber($attribute, $params) {
		$str_card = $this->$attribute;
		$str_card_len = strlen($str_card);
		if ($str_card_len < 16 || $str_card_len > 19) {
			$this->addError($attribute, "你输入的银行卡号有误");
		} else {
			return TRUE;
		}
	}

	/*
	 *银行卡号保护 
	 */

	public static function safetyCardNumber($banks_card_number) {
		$strlengs = strlen($banks_card_number);
		$sub_left = substr($banks_card_number, 0, 3);
		$sub_right = substr($banks_card_number, -4);
		if ($strlengs == 16) {
			$banks_card_number = $sub_left . '********' . $sub_right;
		} elseif ($strlengs == 17) {
			$banks_card_number = $sub_left . '*********' . $sub_right;
		} elseif ($strlengs == 18) {
			$banks_card_number = $sub_left . '**********' . $sub_right;
		} elseif ($strlengs == 19) {
			$sub_right = substr($banks_card_number, -3);
			$banks_card_number = $sub_left . '**********' . $sub_right;
		} else {
			
		}
		return $banks_card_number;
	}

}
