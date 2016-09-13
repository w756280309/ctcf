<?php

use common\models\user\RechargeRecord;
use yii\db\Migration;

class m160912_074636_alter_recharge_record_table extends Migration
{
    public function up()
    {
        $this->addColumn(RechargeRecord::tableName(), 'lastCronCheckTime', $this->integer(10));
    }

    public function down()
    {
        $this->dropColumn(RechargeRecord::tableName(), 'lastCronCheckTime');
    }
}
