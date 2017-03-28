<?php

use yii\db\Migration;

class m170327_080015_alter_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'promoId', $this->integer()->null());
    }

    public function down()
    {
        echo "m170327_080015_alter_user cannot be reverted.\n";

        return false;
    }
}
