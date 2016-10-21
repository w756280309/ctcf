<?php

use yii\db\Migration;

class m161020_080654_alter_bao_quan_queue extends Migration
{
    public function up()
    {
        $this->renameColumn('bao_quan_queue', 'proId', 'itemId');
        $this->addColumn('bao_quan_queue', 'itemType', $this->string(20)->defaultValue('loan'));
    }

    public function down()
    {
        echo "m161020_080654_alter_bao_quan_queue cannot be reverted.\n";

        return false;
    }
}
