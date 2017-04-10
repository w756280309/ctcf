<?php

use yii\db\Migration;

class m170410_021913_alter_reward extends Migration
{
    public function up()
    {
        $this->addColumn('reward', 'ref_id', $this->integer()->null());
    }

    public function down()
    {
        echo "m170410_021913_alter_reward cannot be reverted.\n";

        return false;
    }
}
