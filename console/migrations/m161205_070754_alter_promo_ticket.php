<?php

use yii\db\Migration;

class m161205_070754_alter_promo_ticket extends Migration
{
    public function up()
    {
        $this->alterColumn('promo_lottery_ticket', 'source', $this->string(30));
        $this->addColumn('promo_lottery_ticket', 'promo_id', $this->integer());
        $this->addColumn('promo_lottery_ticket', 'drawAt', $this->integer());
    }

    public function down()
    {
        echo "m161205_070754_alter_promo_ticket cannot be reverted.\n";

        return false;
    }
}
