<?php

use yii\db\Migration;

class m160705_090130_alter_ranking_promo extends Migration
{
    public function up()
    {
        $this->addColumn('ranking_promo', 'key', $this->string(50));
        $this->insert('ranking_promo', [
            'title' => 'PC端上线砸金蛋活动',
            'key' => 'PC_LAUNCH_160707',
            'startAt' => time(),
            'endAt' => time()
        ]);
    }

    public function down()
    {
        echo "m160705_090130_alter_ranking_promo cannot be reverted.\n";

        return false;
    }
}
