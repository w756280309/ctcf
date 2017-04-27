<?php

use yii\db\Migration;

class m170426_024359_crm_phone_call extends Migration
{
    public function up()
    {
        $this->createTable('crm_phone_call', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'callTime' => $this->dateTime()->comment('通话开始时间'),
            'recp_id' => $this->integer()->comment('客服ID'),
            'contact_id' => $this->integer(),
            'direction' => $this->string(20),
            'durationSeconds' => $this->integer(),
            'callerName' => $this->string(20)->comment('客户称呼'),
            'content' => $this->text(),
            'gender' => $this->string(1),
        ]);
    }

    public function down()
    {
        echo "m170426_024359_crm_phone_call cannot be reverted.\n";

        return false;
    }
}
