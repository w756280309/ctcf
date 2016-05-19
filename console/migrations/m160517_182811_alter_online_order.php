<?php

use yii\db\Migration;

class m160517_182811_alter_online_order extends Migration
{
    public function up()
    {
        $this->addColumn('online_order', 'userCoupon_id', $this->integer());//用户coupon中的id
        $this->addColumn('online_order', 'couponAmount', $this->decimal(6, 2)->notNull());//代金券金额
        $this->addColumn('online_order', 'paymentAmount', $this->decimal(14, 2)->notNull());//实付金额
    }

    public function down()
    {
        echo "m160517_182811_alter_online_order cannot be reverted.\n";

        return false;
    }
}
