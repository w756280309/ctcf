<?php

use yii\db\Migration;

class m161229_031345_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0022:20000-20',
            'name' => '积分兑换',
            'amount' => 20,
            'minInvest' => 20000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-31',
            'issueEndDate' => '2017-01-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0022:50000-50',
            'name' => '积分兑换',
            'amount' => 50,
            'minInvest' => 50000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-31',
            'issueEndDate' => '2017-01-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0022:100000-120',
            'name' => '积分兑换',
            'amount' => 120,
            'minInvest' => 100000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-31',
            'issueEndDate' => '2017-01-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0022:200000-180',
            'name' => '积分兑换',
            'amount' => 180,
            'minInvest' => 200000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-31',
            'issueEndDate' => '2017-01-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m161229_031345_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
