<?php

use yii\db\Migration;

/**
 * Handles the creation for table `coins_record`.
 */
class m161227_093151_create_coins_record_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('coins_record', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(),
            'incrCoins' => $this->integer()->notNull(),
            'finalCoins' => $this->integer()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('coins_record');
    }
}
