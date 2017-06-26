<?php

use yii\db\Migration;

/**
 * Handles the creation of table `retention`.
 */
class m170621_031033_create_retention_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('retention', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'seq' => $this->integer()->notNull(),
            'tactic_id' => $this->integer(),
            'status' => $this->string(20)->notNull(),
            'startTime' => $this->dateTime(),
            'createTime' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('uniq_tactic_user_seq', 'retention', ['tactic_id', 'user_id', 'seq'], true);
        $this->createIndex('idx_status', 'retention', ['status']);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('retention');
    }
}
