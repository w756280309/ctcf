<?php

use yii\db\Migration;

class m161025_022306_alter_ebao_quan extends Migration
{
    public function up()
    {
        $this->alterColumn('bao_quan_queue', 'itemId', $this->integer());
        $this->renameColumn('ebao_quan', 'orderId', 'itemId');
        $this->addColumn('ebao_quan', 'itemType', $this->string(20)->defaultValue('loan_order'));
        $this->update('ebao_quan', ['type' => 0]);
    }

    public function down()
    {
        echo "m161025_022306_alter_ebao_quan cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
