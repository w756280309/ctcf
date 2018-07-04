<?php

use yii\db\Migration;

class m180627_063440_alter_online_product extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'asset_id', $this->integer()->null()->defaultValue(null)->unique()->comment('资产包id'));
    }

    public function down()
    {
        echo "m180627_063440_alter_online_product cannot be reverted.\n";

        return false;
    }
}
