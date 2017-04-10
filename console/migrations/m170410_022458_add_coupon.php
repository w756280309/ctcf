<?php

use yii\db\Migration;

class m170410_022458_add_coupon extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'name' => '限时砸金蛋',
            'amount' => 20,
            'minInvest' => 20000,
            'useStartDate' => '',//用户领取当天
            'useEndDate' => '',//30天有效期，包含用户领取当天
            'issueStartDate' => '2017-04-10',
            'issueEndDate' => '2017-04-13',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m170410_022458_add_coupon cannot be reverted.\n";

        return false;
    }
}
