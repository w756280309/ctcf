<?php

use yii\db\Migration;

/**
 * Handles the creation of table `item_message`.
 */
class m180428_024953_create_item_message_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('item_message', [
            'id' => $this->primaryKey(),
            'ticketId' => $this->integer()->comment('抽奖机会id'),
            'content' => $this->string()->comment('描述内容'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('item_message');
    }
}
