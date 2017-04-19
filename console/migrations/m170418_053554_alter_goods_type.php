<?php

use yii\db\Migration;

class m170418_053554_alter_goods_type extends Migration
{
    public function up()
    {
        $this->addColumn('goods_type', 'isSkuEnabled', $this->boolean()->defaultValue(false));
        $this->addColumn('goods_type', 'stock', $this->integer()->null());
    }

    public function down()
    {
        echo "m170418_053554_alter_goods_type cannot be reverted.\n";

        return false;
    }
}
