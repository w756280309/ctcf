<?php

use yii\db\Migration;

/**
 * Class m180720_090020_add_more_index_to_award
 */
class m180720_090020_add_more_index_to_award extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-userId',
            'award',
            'user_id'
        );

        $this->createIndex(
            'idx-rewardId',
            'award',
            'reward_id'
        );

        $this->createIndex(
            'idx-ticketId',
            'award',
            'ticket_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-ticketId',
            'award'
        );

        $this->dropIndex(
            'idx-rewardId',
            'award'
        );

        $this->dropIndex(
            'idx-userId',
            'award'
        );
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180720_090020_add_more_index_to_award cannot be reverted.\n";

        return false;
    }
    */
}
