<?php

use yii\db\Migration;

class m170816_061924_alter_user extends Migration
{
    public function Up()
    {
        $this->dropColumn('user', 'mobile');
        $this->dropColumn('user', 'idcard');

    }

    public function Down()
    {
        echo "m170816_061924_alter_user cannot be reverted.\n";

        return false;
    }
}
