<?php

use yii\db\Migration;

class m180517_040837_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'alternativeRepayer', $this->integer()->null()->comment('代偿方'));
        $this->addColumn('online_product', 'borrowerRate', $this->decimal(6, 4)->comment('融资方利率'));
        $this->addColumn('online_product', 'fundReceiver', $this->integer()->null()->comment('用款方'));
    }

    public function safeDown()
    {
        echo "m180517_040837_alter_online_product cannot be reverted.\n";

        return false;
    }
}
