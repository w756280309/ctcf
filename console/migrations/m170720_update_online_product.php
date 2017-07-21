<?php

use yii\db\Migration;

class m170720_update_online_product extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'order_code' => 4,
            'level' => 3,
            ],
            ['sn'=>'P200101','psn'=>'P200100']

        );

    }

    public function down()
    {
        echo "m170720_update_online_product be reverted.\n";

        return false;
    }
}
