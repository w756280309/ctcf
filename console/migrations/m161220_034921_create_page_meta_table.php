<?php

use yii\db\Migration;

/**
 * Handles the creation for table `page_meta`.
 */
class m161220_034921_create_page_meta_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('page_meta', [
            'id' => $this->primaryKey(),
            'alias' => $this->string()->notNull(),
            'url' => $this->string()->notNull()->unique(),
            'href'=>$this->string()->notNull(),
            'title' => $this->string()->notNull(),
            'keywords' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('page_meta');
    }
}
