<?php

use yii\db\Migration;

class m160516_034721_create_user_coupon_table extends Migration
{
    public function up()
    {
        $this->createTable('user_coupon', [
            'id' => $this->primaryKey(),
            'couponType_id' => $this->integer(10)->notNull(),
            'user_id' => $this->integer(10)->notNull(),
            'order_id' => $this->integer(10)->unique(),
            'isUsed' => $this->boolean()->notNull(),
            'created_at' => $this->integer(10)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user_coupon');
    }
}
