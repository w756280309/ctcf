<?php

use yii\db\Migration;

class m161230_105416_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0022:1000-10',
            'name' => '测试用券',
            'amount' => 10,
            'minInvest' => 1000,
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
        echo "m161230_105416_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
