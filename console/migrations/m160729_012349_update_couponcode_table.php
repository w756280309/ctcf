<?php

use yii\db\Migration;

class m160729_012349_update_couponcode_table extends Migration
{
    public function up()
    {
        $this->addColumn('coupon_code', 'couponType_sn', $this->string());
    }

    public function down()
    {
        echo "m160729_012349_update_couponcode_table cannot be reverted.\n";

        return false;
    }
}
