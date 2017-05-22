<?php

use yii\db\Migration;

class m170516_055250_alter_online_product extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'allowTransfer', $this->boolean()->defaultValue(true));
    }

    public function down()
    {
        echo "m170516_055250_alter_online_product cannot be reverted.\n";

        return false;
    }
}
