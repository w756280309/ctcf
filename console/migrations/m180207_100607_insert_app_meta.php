<?php

use yii\db\Migration;

class m180207_100607_insert_app_meta extends Migration
{
    public function up()
    {
        $this->insert('app_meta', [
            'name' => '解绑银行卡白名单',
            'key' => 'unbind_white',
            'value' => '45035',
        ]);
    }

    public function down()
    {
        echo "m180207_100607_insert_app_meta cannot be reverted.\n";

        return false;
    }
}
