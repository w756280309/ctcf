<?php

use yii\db\Migration;

class m170314_031905_alter_goods_type extends Migration
{
    public function up()
    {
        $this->addColumn('goods_type', 'effectDays', $this->smallInteger());
        $this->addColumn('goods_type', 'affiliator_id', $this->integer());
    }

    public function down()
    {
        echo "m170314_031905_alter_goods_type cannot be reverted.\n";

        return false;
    }
}
