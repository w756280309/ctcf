<?php

use yii\db\Migration;

class m171031_032036_alter_coupon_type extends Migration
{
    public function Up()
    {
        $this->addColumn('coupon_type', 'type', $this->boolean()->defaultValue(0)->comment('0:代金券;1:加息券'));
        $this->addColumn('coupon_type', 'bonusRate', $this->decimal(6,3));
        $this->alterColumn('coupon_type', 'amount', $this->decimal(6,2)->defaultValue(0));
    }

    public function Down()
    {
        echo "m171031_032036_alter_coupon_type cannot be reverted.\n";

        return false;
    }

}
