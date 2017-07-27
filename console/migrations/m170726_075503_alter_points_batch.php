<?php

use yii\db\Migration;

class m170726_075503_alter_points_batch extends Migration
{
    public function Up()
    {
        $this->addColumn('points_batch', 'idCard', $this->string(100)->notNull());
    }

    public function Down()
    {
        echo "m170726_075503_alter_points_batch cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170726_075503_alter_points_batch cannot be reverted.\n";

        return false;
    }
    */
}
