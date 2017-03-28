<?php

use yii\db\Migration;

class m170327_080022_alter_promo extends Migration
{
    public function up()
    {
        $this->addColumn('promo', 'isO2O', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170327_080022_alter_promo cannot be reverted.\n";

        return false;
    }
}
