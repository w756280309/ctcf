<?php

use yii\db\Migration;

class m170511_062150_alter_voucher extends Migration
{
    public function up()
    {
        $this->addColumn('voucher', 'orderNum', $this->string()->unique());
    }

    public function down()
    {
        echo "m170511_062150_alter_voucher cannot be reverted.\n";

        return false;
    }
}
