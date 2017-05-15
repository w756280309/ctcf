<?php

use yii\db\Migration;

class m170512_082818_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101800',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '0元夺宝活动',
            'path' => 'adv/ranking/add-fake',
            'type' => '1',
            'auth_description' => '0元夺宝活动增加活动参与人数',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170512_082818_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
