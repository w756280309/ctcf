<?php

use yii\db\Migration;

class m161101_073418_alter_bao_quan_queue extends Migration
{
    public function up()
    {
        $this->dropIndex('proId','bao_quan_queue');
    }

    public function down()
    {
        echo "m161101_073418_alter_bao_quan_queue cannot be reverted.\n";

        return false;
    }

}
