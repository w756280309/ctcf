<?php

use yii\db\Migration;

class m160706_125347_alter_promo_lottery_ticket extends Migration
{
    public function up()
    {
        $this->addColumn('promo_lottery_ticket', 'source', $this->integer(1));//记录机会来源
    }

    public function down()
    {
        echo "m160706_125347_alter_promo_lottery_ticket cannot be reverted.\n";

        return false;
    }
}
