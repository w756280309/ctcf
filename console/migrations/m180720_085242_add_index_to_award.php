<?php

use yii\db\Migration;

/**
 * Class m180720_085242_add_index_to_award
 */
class m180720_085242_add_index_to_award extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex(
            'idx-promoId',
            'award',
            'promo_id'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex(
            'idx-promoId',
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
        echo "m180720_085242_add_index_to_award cannot be reverted.\n";

        return false;
    }
    */
}
