<?php

use yii\db\Migration;

class m170712_022654_alter_promo_lottery_ticket extends Migration
{
    public function safeUp()
    {
        $this->addColumn('promo_lottery_ticket', 'expiryTime', $this->dateTime()->null());
    }

    public function safeDown()
    {
        echo "m170712_022654_alter_promo_lottery_ticket cannot be reverted.\n";

        return false;
    }
}
