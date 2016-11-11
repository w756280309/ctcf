<?php

use yii\db\Migration;

class m161111_071621_delete_auth_table extends Migration
{
    public function up()
    {
        $this->delete('auth', ['path' => 'user/drawrecord/examinfk']);
    }

    public function down()
    {
        echo "m161111_071621_delete_auth_table cannot be reverted.\n";

        return false;
    }
}
