<?php

use yii\db\Migration;

class m161205_161756_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0018:1000-5',
            'name' => '5元券',
            'amount' => 5.00,
            'minInvest' => 1000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0018:2000-10',
            'name' => '10元券',
            'amount' => 10.00,
            'minInvest' => 2000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0018:5000-25',
            'name' => '25元券',
            'amount' => 25.00,
            'minInvest' => 5000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0018:10000-10',
            'name' => '10元券',
            'amount' => 10.00,
            'minInvest' => 10000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0018:20000-20',
            'name' => '20元券',
            'amount' => 20.00,
            'minInvest' => 20000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0018:50000-50',
            'name' => '50元券',
            'amount' => 50.00,
            'minInvest' => 50000.00,
            'useStartDate' => '',
            'useEndDate' => '',
            'issueStartDate' => '2016-12-12',
            'issueEndDate' => '2016-12-21',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m161205_161756_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
