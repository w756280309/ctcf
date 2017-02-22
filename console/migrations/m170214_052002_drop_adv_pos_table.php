<?php

use yii\db\Migration;

/**
 * Handles the dropping for table `adv_pos`.
 */
class m170214_052002_drop_adv_pos_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropTable('adv_pos');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m170214_052002_drop_adv_pos_table cannot be reverted.\n";

        return false;
    }
}
