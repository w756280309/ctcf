<?php

use yii\db\Migration;

class m171101_020353_alter_coupon_type extends Migration
{
    public function Up()
    {
        $this->addColumn('coupon_type', 'bonusDays', $this->integer()->defaultValue(0)->comment('加息券的加息天数'));
    }

    public function Down()
    {
        echo "m171101_020353_alter_coupon_type cannot be reverted.\n";

        return false;
    }
}
