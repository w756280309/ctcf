<?php

use yii\db\Migration;

class m170313_062041_alter_coupon_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('coupon_type', 'loanExpires', $this->integer());
    }

    public function down()
    {
        echo "m170313_062041_alter_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
