<?php

use yii\db\Migration;

class m171116_032108_add_column_pkg_sn_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'pkg_sn', $this->string(30)->comment('资产包sn'));
    }

    public function safeDown()
    {
        echo "m171116_032108_add_column_pkg_sn_online_product cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171116_032108_add_column_pkg_sn_online_product cannot be reverted.\n";

        return false;
    }
    */
}
