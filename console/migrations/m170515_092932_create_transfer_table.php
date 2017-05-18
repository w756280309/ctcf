<?php

use yii\db\Migration;

/**
 * Handles the creation of table `transfer`.
 */
class m170515_092932_create_transfer_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('transfer', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'updateTime' => $this->dateTime()->null(),
            'amount' => $this->decimal(14, 2)->notNull(),
            'metadata' => $this->string(500)->notNull(),
            'status' => $this->string(10)->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('transfer');
    }
}
