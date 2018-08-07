<?php

use yii\db\Migration;

/**
 * Handles the creation of table `original_borrower`.
 */
class m180731_081915_create_original_borrower_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('original_borrower', [
            'id' => $this->primaryKey()->comment('底层融资方ID'),
            'name' => $this->string(100)->notNull()->unique()->comment('底层融资方名字'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('original_borrower');
    }
}
