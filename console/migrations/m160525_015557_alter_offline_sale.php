<?php

use yii\db\Migration;

class m160525_015557_alter_offline_sale extends Migration
{
    public function up()
    {
        $this->addColumn('ranking_promo_offline_sale', 'investedAt', $this->dateTime());
    }

    public function down()
    {
        echo "m160525_015557_alter_offline_sale cannot be reverted.\n";
        return false;
    }
}
