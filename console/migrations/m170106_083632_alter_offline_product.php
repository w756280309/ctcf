<?php

use yii\db\Migration;

class m170106_083632_alter_offline_product extends Migration
{
    public function up()
    {
        $this->truncateTable('offline_loan');
        $this->addColumn('offline_loan', 'expires', $this->smallInteger(6)->notNull());
        $this->addColumn('offline_loan', 'unit', $this->string(20));
    }

    public function down()
    {
        echo "m170106_083632_alter_offline_product cannot be reverted.\n";

        return false;
    }
}
