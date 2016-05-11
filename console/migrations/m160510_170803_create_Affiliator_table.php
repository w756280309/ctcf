<?php

use yii\db\Migration;

class m160510_170803_create_Affiliator_table extends Migration
{
    public function up()
    {
        $this->createTable('affiliator', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);
    }

    public function down()
    {
        $this->dropTable('affiliator');
    }
}
