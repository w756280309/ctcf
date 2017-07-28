<?php

use yii\db\Migration;

class m170728_032330_alter_table_adv extends Migration
{
    public function Up()
    {
        $this->addColumn('adv', 'start_date', $this->dateTime()->null());
        $this->addColumn('adv', 'timing', $this->boolean()->defaultValue(false));

    }

    public function Down()
    {
        echo "m170728_032330_alter_table_adv cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m170728_032330_alter_table_adv cannot be reverted.\n";

        return false;
    }
    */
}
