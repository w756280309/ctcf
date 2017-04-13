<?php

use yii\db\Migration;

class m170413_091408_add_coupon extends Migration
{
    public function up()
    {
        $time = time();
        $this->insert('coupon_type', [
            'sn' => 'check_in_10000_10',
            'name' => '连续签到',
            'amount' => 10,
            'minInvest' => 10000,
            'issueStartDate' => '2017-03-20',
            'issueEndDate' => '2018-03-20',
            'isDisabled' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'expiresInDays' => 30,
            'isAudited' => 1,
            'useStartDate' => '',
            'useEndDate' => '',
            'customerType' => '',
            'loanCategories' => '',
            'allowCollect' => 0,
            'isAppOnly' => 0,
            'loanExpires' => '',
        ]);

        $this->insert('coupon_type', [
            'sn' => 'check_in_20000_20',
            'name' => '连续签到',
            'amount' => 20,
            'minInvest' => 20000,
            'issueStartDate' => '2017-03-20',
            'issueEndDate' => '2018-03-20',
            'isDisabled' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'expiresInDays' => 30,
            'isAudited' => 1,
            'useStartDate' => '',
            'useEndDate' => '',
            'customerType' => '',
            'loanCategories' => '',
            'allowCollect' => 0,
            'isAppOnly' => 0,
            'loanExpires' => '',
        ]);

        $this->insert('coupon_type', [
            'sn' => 'check_in_50000_50',
            'name' => '连续签到',
            'amount' => 50,
            'minInvest' => 50000,
            'issueStartDate' => '2017-03-20',
            'issueEndDate' => '2018-03-20',
            'isDisabled' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'expiresInDays' => 30,
            'isAudited' => 1,
            'useStartDate' => '',
            'useEndDate' => '',
            'customerType' => '',
            'loanCategories' => '',
            'allowCollect' => 0,
            'isAppOnly' => 0,
            'loanExpires' => '',
        ]);
    }

    public function down()
    {
        echo "m170413_091408_add_coupon cannot be reverted.\n";

        return false;
    }
}
