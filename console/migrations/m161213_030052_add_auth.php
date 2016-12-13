<?php

use yii\db\Migration;

class m161213_030052_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100605',
            'psn' => 'A100600',
            'level' => '3',
            'auth_name' => '导出获奖列表',
            'path' => 'adv/ranking/export-award',
            'type' => '2',
            'auth_description' => '导出获奖列表',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161213_030052_add_auth cannot be reverted.\n";

        return false;
    }
}
