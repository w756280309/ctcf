<?php

use yii\db\Migration;
use common\models\coupon\CouponType;

class m161220_070100_add_coupon extends Migration
{
    public function up()
    {
        //正式环境代金券已经被添加，且代金券已经被使用
        $coupon = CouponType::findOne(['sn' => '0020:50000-70']);
        if (empty($coupon)) {
            $this->insert('coupon_type', [
                'sn' => '0020:50000-70',
                'name' => '70元生日感恩券',
                'amount' => 70,
                'minInvest' => 50000,
                'useStartDate' => '',//用户领取当天
                'useEndDate' => '',//180天有效期，包含用户领取当天
                'issueStartDate' => '2016-11-15',
                'issueEndDate' => '2020-11-15',
                'isDisabled' => 0,
                'created_at' => time(),
                'updated_at' => time(),
                'expiresInDays' => 180,
                'isAudited' => 1,
            ]);
        }

        $coupon = CouponType::findOne(['sn' => '0020:30000-30']);
        if (empty($coupon)) {
            $this->insert('coupon_type', [
                'sn' => '0020:30000-30',
                'name' => '30元生日感恩券',
                'amount' => 30,
                'minInvest' => 30000,
                'useStartDate' => '',//用户领取当天
                'useEndDate' => '',//180天有效期，包含用户领取当天
                'issueStartDate' => '2016-11-15',
                'issueEndDate' => '2020-11-15',
                'isDisabled' => 0,
                'created_at' => time(),
                'updated_at' => time(),
                'expiresInDays' => 180,
                'isAudited' => 1,
            ]);
        }

        $coupon = CouponType::findOne(['sn' => '0020:20000-20']);
        if (empty($coupon)) {
            $this->insert('coupon_type', [
                'sn' => '0020:20000-20',
                'name' => '20元生日感恩券',
                'amount' => 20,
                'minInvest' => 20000,
                'useStartDate' => '',//用户领取当天
                'useEndDate' => '',//180天有效期，包含用户领取当天
                'issueStartDate' => '2016-11-15',
                'issueEndDate' => '2020-11-15',
                'isDisabled' => 0,
                'created_at' => time(),
                'updated_at' => time(),
                'expiresInDays' => 180,
                'isAudited' => 1,
            ]);
        }
    }

    public function down()
    {
        echo "m161220_070100_add_coupon cannot be reverted.\n";

        return false;
    }
}
