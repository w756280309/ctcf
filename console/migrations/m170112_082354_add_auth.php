<?php

use yii\db\Migration;

class m170112_082354_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100307',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '发放积分',
            'path' => 'user/point/add',
            'type' => '2',
            'auth_description' => '发放积分',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170112_082354_add_auth cannot be reverted.\n";

        return false;
    }
}
