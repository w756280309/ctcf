<?php

use yii\db\Migration;

class m180530_061951_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'guarantee', $this->integer()->null()->comment('担保方'));
    }

    public function safeDown()
    {
        echo "m180530_061951_alter_online_product cannot be reverted.\n";

        return false;
    }
}
