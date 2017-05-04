<?php

use yii\db\Migration;

class m170503_100626_alter_promo_lottery_ticket_table extends Migration
{
    public function up()
    {
        $this->addColumn('promo_lottery_ticket', 'joinSequence', $this->integer());
        $this->addColumn('promo_lottery_ticket', 'duobaoCode', $this->string());
    }

    public function down()
    {
        echo "m170503_100626_alter_promo_lottery_ticket_table cannot be reverted.\n";

        return false;
    }
}
