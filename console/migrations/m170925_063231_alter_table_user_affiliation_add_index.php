<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_affiliation`.
 */
class m170925_063231_alter_table_user_affiliation_add_index extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createIndex(
            'affiliator_id',
            'user_affiliation',
            'affiliator_id'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m170925_063231_alter_table_user_affiliation_add_index cannot be reverted.\n";

        return false;
    }
}
