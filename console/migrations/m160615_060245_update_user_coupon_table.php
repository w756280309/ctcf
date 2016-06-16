<?php

use yii\db\Migration;

class m160615_060245_update_user_coupon_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_coupon', 'expiryDate', $this->date()->notNull());
    }

    public function down()
    {
        $this->dropColumn('user_coupon', 'expiryDate');
    }
}