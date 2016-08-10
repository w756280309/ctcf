<?php

use yii\db\Migration;

class m160809_092045_create_promo0809_log_table extends Migration
{
    public function up()
    {
        $this->createTable('promo0809_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'prize_id' => $this->integer(1)->notNull(),
            'user_address' => $this->string()->notNull(),
            'createdAt' => $this->date()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('promo0809_log');
    }
}
