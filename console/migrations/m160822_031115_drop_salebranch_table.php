<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `branch`.
 */
class m160822_031115_drop_salebranch_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('sale_branch');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->createTable('sale_branch', [
            'id' => $this->primaryKey(),
        ]);
    }
}
