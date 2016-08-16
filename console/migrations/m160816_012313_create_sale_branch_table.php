<?php

use yii\db\Migration;

class m160816_012313_create_sale_branch_table extends Migration
{
    public function up()
    {
        $this->createTable('sale_branch', [
            'id' => $this->primaryKey(),
            'branchName' => $this->string(255)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('sale_branch');
    }
}
