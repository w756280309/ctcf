<?php

use yii\db\Migration;

class m170308_114553_add_goods_type extends Migration
{
    public function up()
    {
        $this->insert('goods_type', [
            'sn' => 'yikecoffee',
            'name' => '意克咖啡70元美食券',
            'type' => 3,
            'createdAt' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        echo "m170308_114553_add_goods_type cannot be reverted.\n";

        return false;
    }
}
