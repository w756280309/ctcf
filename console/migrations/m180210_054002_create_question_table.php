<?php

use yii\db\Migration;

/**
 * Handles the creation of table `person`.
 */
class m180210_054002_create_question_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('question', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull()->comment('问题'),
            'batchSn' => $this->string(20)->notNull()->comment('批次号'),
            'promoId' => $this->integer()->null()->comment('活动ID'),
            'answer' => $this->string()->null()->comment('答案'),
            'createTime' => $this->dateTime()->null()->comment('创建时间'),
            'updateTime' => $this->dateTime()->null()->comment('修改时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('question');
    }
}
