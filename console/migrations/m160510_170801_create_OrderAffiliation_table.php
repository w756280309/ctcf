<?php

use yii\db\Migration;

class m160510_170801_create_OrderAffiliation_table extends Migration
{
    public function up()
    {
        $this->createTable('order_affiliation', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'trackCode' => $this->string(),
            'affiliator_id' => $this->integer()
        ]);
    }

    public function down()
    {
        $this->dropTable('order_affiliation');
    }
}
