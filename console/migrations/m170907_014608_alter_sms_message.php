<?php

use yii\db\Migration;

class m170907_014608_alter_sms_message extends Migration
{
    public function safeUp()
    {
        $this->addColumn('sms_message', 'serviceProvider', $this->string(32)->defaultValue(\common\service\SmsService::DEFAULT_SERVICE_PROVIDER)->comment('短信服务供应商'));
    }

    public function safeDown()
    {
        echo "m170907_014608_alter_sms_message cannot be reverted.\n";

        return false;
    }
}
