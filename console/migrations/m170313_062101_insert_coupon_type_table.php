<?php

use yii\db\Migration;

class m170313_062101_insert_coupon_type_table extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0015:50000-50',
            'name' => '幸运券',
            'amount' => 50,
            'minInvest' => 50000,
            'issueStartDate' => date('Y-m-d'),
            'issueEndDate' => date('Y-m-d', strtotime('now + 1 year')),
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 15,
            'allowCollect' => 1,
            'isAudited' => 1,
            'isAppOnly' => 0,
            'loanExpires' => 100,
        ]);

        $this->insert('coupon_type', [
            'sn' => '0015:100000-88',
            'name' => '幸运券',
            'amount' => 88,
            'minInvest' => 100000,
            'issueStartDate' => date('Y-m-d'),
            'issueEndDate' => date('Y-m-d', strtotime('now + 1 year')),
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 15,
            'allowCollect' => 1,
            'isAudited' => 1,
            'isAppOnly' => 0,
            'loanExpires' => 100,
        ]);

        $this->insert('coupon_type', [
            'sn' => '0015:200000-168',
            'name' => '幸运券',
            'amount' => 168,
            'minInvest' => 200000,
            'issueStartDate' => date('Y-m-d'),
            'issueEndDate' => date('Y-m-d', strtotime('now + 1 year')),
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 15,
            'allowCollect' => 1,
            'isAudited' => 1,
            'isAppOnly' => 0,
            'loanExpires' => 100,
        ]);
    }

    public function down()
    {
        echo "m170313_062101_insert_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
