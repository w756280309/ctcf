<?php

use yii\db\Migration;

/**
 * Handles the creation of table `second_kill`.
 */
class m171026_005507_create_second_kill_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('second_kill', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer()->unsigned()->notNull()->comment('用户ID '),
            'createTime' => $this->integer()->unsigned()->notNull()->comment('获奖时间'),
            'term' => $this->string(10)->notNull()->comment('物品编号'),
        ]);
        // creates index for column `user_id and term`
        $this->createIndex(
            'term_2',
            'second_kill',
            'userId,term',
            '1'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('second_kill');
    }
}
