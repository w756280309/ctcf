<?php

use yii\db\Migration;

class m171110_084423_insert_auth extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'H100116',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '删除优惠券',
            'path' => 'coupon/coupon/del',
            'type' => '2',
            'auth_description' => '删除用户优惠券,使其过期',
            'status' => '2',
            'order_code' => '3',
        ]);
    }

    public function Down()
    {
        echo "m171110_084423_insert_auth cannot be reverted.\n";

        return false;
    }

}
