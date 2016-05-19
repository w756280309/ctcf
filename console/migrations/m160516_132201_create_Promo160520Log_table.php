<?php

use yii\db\Migration;

class m160516_132201_create_Promo160520Log_table extends Migration
{
    public function up()
    {
        $this->createTable('promo160520_log', [
            'id' => $this->primaryKey(),
            'mobile' => $this->string(20)->notNull()->unique(),
            'prizeId' => $this->smallInteger(1)->notNull(),
            'isNewUser' => $this->boolean()->notNull(),
            'count' => $this->smallInteger(1)->notNull(),
            'createdAt' => $this->integer(11)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('promo160520_log');
    }
}
