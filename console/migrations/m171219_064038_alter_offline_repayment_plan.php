<?php

use yii\db\Migration;

class m171219_064038_alter_offline_repayment_plan extends Migration
{
    public function up()
    {
        $this->addColumn('offline_repayment_plan', 'isSendSms', $this->boolean()->notNull()->defaultValue(0)->comment('是否发送短信'));
    }

    public function down()
    {
        echo "m171219_064038_alter_offline_repayment_plan cannot be reverted.\n";

        return false;
    }
}
