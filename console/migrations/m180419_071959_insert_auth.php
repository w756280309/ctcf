<?php

use yii\db\Migration;

/**
 * Class m180419_071959_insert_auth
 */
class m180419_071959_insert_auth extends Migration
{

    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100312',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '查看受让人投资信息',
            'path' => 'user/user/invest-info',
            'type' => '2',
            'auth_description' => '查看受让人投资信息（目前手机号及投资金额）',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m180419_071959_insert_auth cannot be reverted.\n";

        return false;
    }

}
