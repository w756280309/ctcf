<?php

use common\models\user\DrawRecord;
use yii\db\Migration;

class m160912_075320_alter_draw_record_table extends Migration
{
    public function up()
    {
        $this->addColumn(DrawRecord::tableName(), 'lastCronCheckTime', $this->integer(10));
    }

    public function down()
    {
        $this->dropColumn(DrawRecord::tableName(), 'lastCronCheckTime');
    }
}
