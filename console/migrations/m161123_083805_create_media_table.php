<?php

use yii\db\Migration;

/**
 * Handles the creation for table `media`.
 */
class m161123_083805_create_media_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('media', [
            'id' => $this->primaryKey(),
            'type' => $this->string()->notNull(),
            'uri' => $this->string()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('media');
    }
}
