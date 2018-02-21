<?php

use yii\db\Migration;

/**
 * Handles the creation of table `option`.
 */
class m180210_054018_create_option_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('option', [
            'id' => $this->primaryKey(),
            'questionId' => $this->integer()->notNull()->comment('题目ID'),
            'content' => $this->text()->notNull()->comment('选项内容'),
            'createTime' => $this->dateTime()->null()->comment('创建时间'),
            'updateTime' => $this->dateTime()->null()->comment('修改时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('option');
    }
}
