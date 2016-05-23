<?php

use yii\db\Migration;

class m160518_065317_ranking_promo extends Migration
{
    public function up()
    {
        $this->createTable('ranking_promo', [
            'id' => $this->primaryKey(),
            'title' => $this->string(50)->notNull(),
            'startAt' => $this->integer(10)->notNull(),
            'endAt' => $this->integer(10)->notNull()
        ]);
    }

    public function down()
    {
        $this->dropTable('ranking_promo');
    }
}
