<?php

use yii\db\Migration;

class m170724_063903_alter_voucher extends Migration
{
    public function safeUp()
    {
        $this->addColumn('voucher', 'expireTime', $this->dateTime()->null());
        $this->addColumn('voucher', 'isOp', $this->boolean()->defaultValue(false));
        $this->addColumn('voucher', 'amount', $this->decimal(14, 2)->null());
        $this->alterColumn('voucher', 'goodsType_sn', $this->string()->null());
    }

    public function safeDown()
    {
        echo "m170724_063903_alter_voucher cannot be reverted.\n";

        return false;
    }
}
