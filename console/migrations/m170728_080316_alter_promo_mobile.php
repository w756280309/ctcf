<?php

use yii\db\Migration;

class m170728_080316_alter_promo_mobile extends Migration
{
    public function safeUp()
    {
        $this->addColumn('promo_mobile', 'referralSource', $this->string(50)->null());
        $this->alterColumn('promo_mobile', 'promo_id', $this->integer()->null());
    }

    public function safeDown()
    {
        echo "m170728_080316_alter_promo_mobile cannot be reverted.\n";

        return false;
    }
}
