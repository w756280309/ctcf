<?php

use yii\db\Migration;

class m160527_054319_update_affiliator_table extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'picPath', $this->string());
    }

    public function down()
    {
        $this->dropColumn('affiliator', 'picPath');
    }
}
