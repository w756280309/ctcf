<?php

use yii\db\Migration;

class m160730_113145_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0012:10000-20',
            'name' => '20元券',
            'amount' => 20.00,
            'minInvest' => 10000.00,
            'useStartDate' => '',
            'useEndDate' => '2016-09-28',//包含28号
            'issueStartDate' => '2016-08-02',
            'issueEndDate' => '2016-09-28',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 0,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0012:50000-40',
            'name' => '40元券',
            'amount' => 40.00,
            'minInvest' => 50000.00,
            'useStartDate' => '',
            'useEndDate' => '2016-09-28',//包含28号
            'issueStartDate' => '2016-08-02',
            'issueEndDate' => '2016-09-28',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 0,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0012:50000-50',
            'name' => '50元券',
            'amount' => 50.00,
            'minInvest' => 50000.00,
            'useStartDate' => '',
            'useEndDate' => '2016-09-28',//包含28号
            'issueStartDate' => '2016-08-02',
            'issueEndDate' => '2016-09-28',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 0,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
        $this->insert('coupon_type', [
            'sn' => '0012:100000-90',
            'name' => '90元券',
            'amount' => 90.00,
            'minInvest' => 100000.00,
            'useStartDate' => '',
            'useEndDate' => '2016-09-28',//包含28号
            'issueStartDate' => '2016-08-02',
            'issueEndDate' => '2016-09-28',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 0,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m160730_113145_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
