<?php

use yii\db\Migration;

class m160705_092713_add_coupon_type extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0009:1000-28',
            'name' => '28元券',
            'amount' => 28.00,
            'minInvest' => 1000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-07-08',
            'issueEndDate' => '2016-07-22',
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
            'sn' => '0010:10000-50',
            'name' => '50元券',
            'amount' => 50.00,
            'minInvest' => 10000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-07-08',
            'issueEndDate' => '2016-07-22',
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
            'sn' => '0010:50000-90',
            'name' => '90元券',
            'amount' => 90.00,
            'minInvest' => 50000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-07-08',
            'issueEndDate' => '2016-07-22',
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
            'sn' => '0010:100000-120',
            'name' => '120元券',
            'amount' => 120.00,
            'minInvest' => 100000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-07-08',
            'issueEndDate' => '2016-07-22',
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
            'sn' => '0010:200000-180',
            'name' => '180元券',
            'amount' => 180.00,
            'minInvest' => 200000.00,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2016-07-08',
            'issueEndDate' => '2016-07-22',
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
        echo "m160705_092713_add_coupon_type cannot be reverted.\n";

        return false;
    }
}
