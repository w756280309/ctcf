<?php

use yii\db\Migration;

class m170614_054708_alter_loan extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'isCustomRepayment', $this->boolean()->comment('是否自定义还款'));
    }

    public function down()
    {
        echo "m170614_054708_alter_loan cannot be reverted.\n";

        return false;
    }
}
