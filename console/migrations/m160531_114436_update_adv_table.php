<?php

use yii\db\Migration;

class m160531_114436_update_adv_table extends Migration
{
    public function up()
    {
        $this->addColumn('adv', 'showOnPc', $this->boolean());
    }

    public function down()
    {
        $this->dropColumn('adv', 'showOnPc');
    }
}
