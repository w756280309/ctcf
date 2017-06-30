<?php

use yii\db\Migration;

class m170629_125123_alter_user_coupon_table extends Migration
{
    public function up()
    {
        $this->dropIndex('order_id', 'user_coupon');
    }

    public function down()
    {
        echo "m170629_125123_alter_user_coupon_table cannot be reverted.\n";

        return false;
    }
}
