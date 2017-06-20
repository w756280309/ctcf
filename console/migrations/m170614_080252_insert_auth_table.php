<?php

use yii\db\Migration;

class m170614_080252_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101902',
            'psn' => 'A101900',
            'level' => '3',
            'auth_name' => '外链编辑',
            'path' => 'source/referral-source/update',
            'type' => '2',
            'auth_description' => '外链编辑',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170614_080252_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
