<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "money_record".
 *
 * @property string $id
 * @property string $sn
 * @property integer $type
 * @property string $osn
 * @property string $account_id
 * @property string $uid
 * @property string $in_money
 * @property string $out_money
 * @property string $remark
 * @property string $create_at
 * @property string $updated_at
 */
class MoneyRecord extends \yii\db\ActiveRecord {




	//类型。0-充值，1-提现，2-投标，3，放款，4，还款
	const TYPE_RECHARGE = 0; //充值
	const TYPE_DRAW = 1; //提现
	const TYPE_ORDER = 2; //投标
	const TYPE_FANGKUAN = 3; //放款
	const TYPE_HUANKUAN = 4; //还款
        const TYPE_CHEBIAO = 5; //撤标
        const TYPE_FEE = 6; //放款扣去手续费
        const TYPE_DRAW_RETURN = 7; //提现退款

//状态。 0-默认未处理的，1-成功，2失败
	const STATUS_ZERO = 0; //未处理
	const STATUS_SUCCESS = 1; //成功
	const STATUS_FAIL = 2; //失败
        const STATUS_REFUND = 3; //流标 退款

	/**
	 * @inheritdoc
	 */

	public static function tableName() {
		return 'money_record';
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors() {
		return [
			TimestampBehavior::className(),
		];
	}

	public static function createSN($pre = '') {
		$pre_val = "ML";
		list($usec, $sec) = explode(" ", microtime());
		$v = ((float) $usec + (float) $sec);

		list($usec, $sec) = explode(".", $v);
		$date = date('ymdHisx' . rand(1000, 9999), $usec);
		return $pre_val . str_replace('x', $sec, $date);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['type', 'account_id', 'uid'], 'integer'],
			[['account_id'], 'required'],
			[['in_money', 'out_money','balance'], 'number'],
			//[['out_money'],'number','min' =>0.01,'message' =>'提现金额必须大于0.01元人民币'],
			[['sn'], 'string', 'max' => 30],
			[['remark'], 'string', 'max' => 500]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'sn' => 'Sn',
			'type' => 'Type',
			'osn' => 'Osn',
			'account_id' => 'Account ID',
			'uid' => 'Uid',
			'in_money' => 'In Money',
			'out_money' => '提现金额',
                        'balance' => '余额',
			'remark' => 'Remark',
			'create_at' => 'Create At',
			'updated_at' => 'Updated At',
		];
	}

}
