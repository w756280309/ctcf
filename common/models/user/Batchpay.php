<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
use common\lib\bchelp\BcRound;

class Batchpay extends \yii\db\ActiveRecord {

    const IS_LAUNCH_YES = 1;//已发起
    const IS_LAUNCH_NO = 0;//未发起
    
    /**
     * 定义表名
     */
    public static function tableName() {
        return 'batchpay';
    }

    /**
     * 设置规则
     */
    public function rules() {
        return [
            [['sn', 'admin_id', 'total_amount', 'total_count', 'payment_flag', 'is_launch'], 'required'],
            [['admin_id', 'total_count', 'payment_flag', 'is_launch'], 'integer'],
            [['total_amount'], 'number'],
            [['sn'], 'string'],
            [['remark'], 'string']
        ];
    }

    public function behaviors() {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 返回字段显示名
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'sn' => '批次号',
            'admin_id' => '管理者ID',
            'total_amount' => '总金额',
            'total_count' => '总数',
            'payment_flag' => '代付标识',
            'is_launch' => '是否发起请求',
            'remark' => '备注',
        ];
    }

    /**
     *
     * @param type $draw
     */
    public function singleInsert($admin_id, $ids) {
        $bc = new BcRound();
        $connection = \Yii::$app->db;
        bcscale(14); //设置小数位数s
        $batch = DrawRecord::find()->where(['id' => $ids, 'status' => DrawRecord::STATUS_EXAMINED])->all();
        $total_count = count($batch);
        $total_sum = 0;
        $batchItem = array();
        foreach ($batch as $draw) {
            $total_sum = bcadd($total_sum, $draw->money);
        }
        $batchPay = new Batchpay([
            'sn' => \PayGate\Cfca\CfcaUtils::generateSn('B'),
            'admin_id' => $admin_id,
            'total_amount' => $bc->bcround($total_sum, 2),
            'total_count' => $total_count,
            'payment_flag' => 1,
            'is_launch' => 0
        ]);
        if ($batchPay->validate() && $batchPay->save(FALSE)) {
            $time = time();
            foreach ($batch as $draw) {
                $batchItem[] = [
                    $batchPay->id,
                    $draw->id,
                    $draw->uid,
                    $draw->money,
                    $draw->account_id,
                    $draw->user_bank_id,
                    $draw->bank_id,
                    $draw->pay_bank_id,
                    BatchpayItem::ACCOUNT_TYPE_PERSONAL,
                    $draw->bank_username,
                    $draw->bank_account,
                    $draw->sub_bank_name,
                    $draw->province,
                    $draw->city,
                    $draw->mobile,
                    $draw->identification_type,
                    $draw->identification_number,
                    BatchpayItem::STATUS_WAIT,
                    0,
                    $time,
                    $time
                ];
            }
            $res = $connection->createCommand()->batchInsert(BatchpayItem::tableName(), [
                        'batchpay_id', 'draw_id', 'uid', 'amount', 'account_id', 'user_bank_id',
                        'bank_id', 'pay_bank_id', 'account_type', 'account_name', 'account_number',
                        'branch_name', 'province', 'city', 'phone_number', 'identification_type',
                        'identification_number', 'status', 'banktxtime','created_at','updated_at'], $batchItem)->execute();
            if (!$res) {
                return false;
            }
        } else {
            return FALSE;
        }
        return true;
    }

    /**
     *
     * 返回 BatchItem
     */
    public function getItems() {
        return $this->hasMany(BatchpayItem::className(), ['batchpay_id' => 'id']);
    }

}
