<?php

use yii\db\Migration;

class m170331_061510_alter_user_coupon_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_coupon', 'admin_id', $this->integer());
        $this->addColumn('user_coupon', 'ip', $this->string());
    }

    public function down()
    {
        echo "m170331_061510_alter_user_coupon_table cannot be reverted.\n";

        return false;
    }
}
