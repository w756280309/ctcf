<?php

use yii\db\Migration;

class m171122_075331_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100803',
            'psn' => 'A100800',
            'level' => '3',
            'auth_name' => '删除分销商',
            'path' => 'fenxiao/fenxiao/del',
            'type' => '2',
            'auth_description' => '删除分销商',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m171122_075331_insert_auth cannot be reverted.\n";

        return false;
    }
}
