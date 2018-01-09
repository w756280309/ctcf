<?php

use yii\db\Migration;

class m180103_030542_create_annual_export extends Migration
{
    public function up()
    {
        $this->createTable('annual_report', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unique()->notNull(),
            'totalProfit' => $this->decimal(14, 2)->defaultValue('0.00'),
        ]);
    }

    public function down()
    {
        echo "m180103_030542_create_annual_export cannot be reverted.\n";

        return false;
    }
}
