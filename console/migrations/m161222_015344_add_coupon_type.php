<?php

use yii\db\Migration;

class m161222_015344_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0021:1000-10',
            'name' => '圣诞砸金蛋',
            'amount' => 10,
            'minInvest' => 1000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:50000-50',
            'name' => '圣诞砸金蛋',
            'amount' => 50,
            'minInvest' => 50000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:1000-28',
            'name' => '圣诞砸金蛋',
            'amount' => 28,
            'minInvest' => 1000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:10000-50',
            'name' => '圣诞砸金蛋',
            'amount' => 50,
            'minInvest' => 10000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:50000-90',
            'name' => '圣诞砸金蛋',
            'amount' => 90,
            'minInvest' => 50000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:100000-120',
            'name' => '圣诞砸金蛋',
            'amount' => 120,
            'minInvest' => 100000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0021:200000-180',
            'name' => '圣诞砸金蛋',
            'amount' => 180,
            'minInvest' => 200000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-24',
            'issueEndDate' => '2016-12-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m161222_015344_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
