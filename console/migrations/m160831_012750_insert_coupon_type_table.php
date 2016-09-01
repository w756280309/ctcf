<?php

use yii\db\Migration;

class m160831_012750_insert_coupon_type_table extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0015:10000-20',
            'name' => '注册奖励',
            'amount' => 20.00,
            'minInvest' => 10000.00,
            'issueStartDate' => '2016-08-31',
            'issueEndDate' => '2116-08-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);

        $this->insert('coupon_type', [
            'sn' => '0015:20000-30',
            'name' => '注册奖励',
            'amount' => 30.00,
            'minInvest' => 20000.00,
            'issueStartDate' => '2016-08-31',
            'issueEndDate' => '2116-08-31',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m160831_012750_insert_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
