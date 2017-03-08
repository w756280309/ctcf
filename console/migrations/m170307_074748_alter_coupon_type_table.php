<?php

use yii\db\Migration;

class m170307_074748_alter_coupon_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('coupon_type', 'isAppOnly', $this->boolean()->notNull());
    }

    public function down()
    {
        echo "m170307_074748_alter_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
