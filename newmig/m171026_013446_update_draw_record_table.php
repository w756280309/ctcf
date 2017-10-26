<?php

use yii\db\Migration;

class m171026_013446_update_draw_record_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('draw_record', 'payoutMethod', $this->smallInteger()->notNull()->comment('提现'));
    }

    public function safeDown()
    {
        echo "m171026_013446_update_draw_record_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m171026_013446_update_draw_record_table cannot be reverted.\n";

        return false;
    }
    */
}
