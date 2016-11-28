<?php

use yii\db\Migration;

class m161125_111434_delete_auth_table extends Migration
{
    public function up()
    {
        $this->delete('auth', ['path' => 'product/issuer/media-edit']);
    }

    public function down()
    {
        echo "m161125_111434_delete_auth_table cannot be reverted.\n";

        return false;
    }
}
