<?php

use yii\db\Migration;

class m170216_012840_alter_promo_table extends Migration
{
    public function up()
    {
        $this->addColumn('promo', 'config', $this->text());
    }

    public function down()
    {
        echo "m170216_012840_alter_promo_table cannot be reverted.\n";

        return false;
    }
}
