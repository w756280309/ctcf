<?php

use yii\db\Migration;

class m170628_075139_alter_online_order extends Migration
{
    public function up()
    {
        $this->dropColumn('online_order', 'userCoupon_id');
    }

    public function down()
    {
        echo "m170628_075139_alter_online_order cannot be reverted.\n";

        return false;
    }
}
