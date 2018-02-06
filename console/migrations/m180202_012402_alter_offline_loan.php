<?php

use yii\db\Migration;

class m180202_012402_alter_offline_loan extends Migration
{
    public function up()
    {
        $this->addColumn('offline_loan', 'isCustomRepayment', $this->boolean()->notNull()->defaultValue(0)->comment('是否自定义还款'));
    }

    public function down()
    {
        echo "m180202_012402_alter_offline_loan cannot be reverted.\n";

        return false;
    }
}
