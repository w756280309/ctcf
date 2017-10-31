<?php

use yii\db\Migration;

class m171031_032052_alter_online_product extends Migration
{
    public function Up()
    {
        $this->addColumn('online_product', 'allowRateCoupon', $this->boolean()->defaultValue(0)->comment('加息券使用0:禁止;1:允许'));
    }

    public function Down()
    {
        echo "m171031_032052_alter_online_product cannot be reverted.\n";

        return false;
    }
}
