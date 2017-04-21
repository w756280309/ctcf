<?php

use yii\db\Migration;

class m170421_115703_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100208',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '确认起息日',
            'path' => 'offline/offline/loan-confirm',
            'type' => '2',
            'auth_description' => '确认起息日',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170421_115703_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
