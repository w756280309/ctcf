<?php

use yii\db\Migration;

class m160816_012532_create_offline_loan_table extends Migration
{
    public function up()
    {
        $this->createTable('offline_loan', [
            'id' => $this->primaryKey(),
            'title'=> $this->string(255)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('offline_loan');
    }
}
