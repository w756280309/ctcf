<?php

use yii\db\Migration;

class m161104_082727_insert_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'path' => 'user/drawrecord/ump-status',
            'auth_name' => '查询提现流水联动状态',
            'auth_description' => '查询提现流水联动状态',
        ], ['path' => 'user/drawrecord/edit']);
    }

    public function down()
    {
        echo "m161104_082727_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
