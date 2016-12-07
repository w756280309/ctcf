<?php

use yii\db\Migration;

class m161205_071051_alter_promo extends Migration
{
    public function up()
    {
        $this->addColumn('promo', 'promoClass', $this->string());
        $this->addColumn('promo', 'whiteList', $this->string());
        $this->addColumn('promo', 'isOnline', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m161205_071051_alter_promo cannot be reverted.\n";

        return false;
    }
}
