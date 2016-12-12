<?php

use yii\db\Migration;

class m161212_051712_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100604',
            'psn' => 'A100600',
            'level' => '3',
            'auth_name' => '活动获奖列表',
            'path' => 'adv/ranking/award-list',
            'type' => '2',
            'auth_description' => '活动获奖列表',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161212_051712_add_auth cannot be reverted.\n";

        return false;
    }
}
