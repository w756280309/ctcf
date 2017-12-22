<?php

use yii\db\Migration;

class m171220_021445_alter_offline_loan extends Migration
{
    public function up()
    {
        $this->addColumn('offline_loan', 'paymentDay', $this->integer()->notNull()->defaultValue(null)->comment('固定还款日'));
    }

    public function down()
    {
        echo "m171220_021445_alter_offline_loan cannot be reverted.\n";

        return false;
    }
}
