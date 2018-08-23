<?php

use yii\db\Migration;

/**
 * Class m180822_033726_insert_auth
 */
class m180822_033726_insert_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'H100119',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '批量删除优惠券',
            'path' => 'coupon/coupon/batch-del',
            'type' => '2',
            'auth_description' => '批量删除用户优惠券,使其过期',
            'status' => '2',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        $this->delete('auth', ['sn' => 'H100119']);

        return true;
    }
}
