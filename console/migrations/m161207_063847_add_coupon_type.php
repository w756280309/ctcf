<?php

use yii\db\Migration;

class m161207_063847_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0019:10000-50',
            'name' => '50元券',
            'amount' => 50,
            'minInvest' => 10000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2017-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);

        $this->insert('coupon_type', [
            'sn' => '0019:10000-30',
            'name' => '30元券',
            'amount' => 30,
            'minInvest' => 10000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2017-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m161207_063847_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
