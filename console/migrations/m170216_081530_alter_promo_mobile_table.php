<?php

use yii\db\Migration;

class m170216_081530_alter_promo_mobile_table extends Migration
{
    public function up()
    {
        $this->addColumn('promo_mobile', 'ip', $this->string()->notNull());
    }

    public function down()
    {
        echo "m170216_081530_alter_promo_mobile_table cannot be reverted.\n";

        return false;
    }
}
