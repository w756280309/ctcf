<?php

use yii\db\Migration;

class m170620_063122_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200119',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '计息审核',
            'path' => 'product/productonline/jixi-examined',
            'type' => '2',
            'auth_description' => '计息审核',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170620_063122_add_auth cannot be reverted.\n";

        return false;
    }
}
