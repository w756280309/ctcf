<?php
use yii\db\Migration;

class m160705_051849_update_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'path' => 'datatj/issuer/lh-list',
        ], ['path' => 'datatj/issuer/list?id=1']);
    }

    public function down()
    {
        echo "m160705_051849_update_auth_table cannot be reverted.\n";

        return false;
    }
}
