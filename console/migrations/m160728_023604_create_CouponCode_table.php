<?php

use yii\db\Migration;

class m160728_023604_create_CouponCode_table extends Migration
{
    public function up()
    {
        $this->createTable('coupon_code', [
            'id' => $this->primaryKey(),
            'code' => $this->string(16)->notNull()->unique(),
            'user_id' => $this->integer(10),
            'isUsed' => $this->integer(1)->notNull(),
            'usedAt' => $this->dateTime(),
            'createdAt' => $this->dateTime()->notNull(),
            'expiresAt' => $this->dateTime()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('coupon_code');
    }
}

