<?php

use yii\db\Migration;

/**
 * Handles the creation for table `offline_stats`.
 */
class m170111_030947_create_offline_stats_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('offline_stats', [
            'id' => $this->primaryKey(),
            'tradedAmount' => $this->decimal(14, 2)->notNull(),
            'refundedPrincipal' => $this->decimal(14, 2)->notNull(),
            'refundedInterest' => $this->decimal(14, 2)->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('offline_stats');
    }
}
