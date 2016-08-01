<?php

use yii\db\Migration;

class m160727_070611_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0011:10000-30',
            'name' => '30元券',
            'amount' => 30.00,
            'minInvest' => 10000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-08-04',//待定
            'issueEndDate' => '2016-09-04',//待定
            'isDisabled' => 0,//有效
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,//已审核
        ]);
        $this->insert('coupon_type', [
            'sn' => '0011:10000-50',
            'name' => '50元券',
            'amount' => 50.00,
            'minInvest' => 10000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-08-04',//待定
            'issueEndDate' => '2016-09-04',//待定
            'isDisabled' => 0,//有效
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'customerType' => null,
            'loanCategories' => null,
            'allowCollect' => 1,
            'isAudited' => 1,//已审核
        ]);
    }

    public function down()
    {
        echo "m160727_070611_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
