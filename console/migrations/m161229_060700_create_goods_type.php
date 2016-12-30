<?php

use yii\db\Migration;

class m161229_060700_create_goods_type extends Migration
{
    public function up()
    {
        $this->createTable('goods_type', [
            'id' => $this->primaryKey(),
            'sn' => $this->string()->unique()->notNull(),
            'name' => $this->string(),
        ]);
    }

    public function down()
    {
        echo "m161229_060700_create_goods_type cannot be reverted.\n";

        return false;
    }
}
