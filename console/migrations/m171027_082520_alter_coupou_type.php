<?php

use yii\db\Migration;

class m171027_082520_alter_coupou_type extends Migration
{
    public function Up()
    {
        $this->addColumn('coupon_type', 'type', $this->boolean()->defaultValue(0)->comment('0:代金券;1:加息券'));
        $this->addColumn('coupon_type', 'increase_interest', $this->decimal(6,3));
        $this->alterColumn('coupon_type', 'amount', $this->decimal(6,2)->defaultValue(0));
    }

    public function Down()
    {
        echo "m171027_082520_alter_coupou_type cannot be reverted.\n";

        return false;
    }
}
