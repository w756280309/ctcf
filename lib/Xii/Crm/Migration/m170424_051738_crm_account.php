<?php

use yii\db\Migration;

class m170424_051738_crm_account extends Migration
{
    public function up()
    {
        $this->createTable('crm_account', [
            'id' => $this->primaryKey(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'primaryContact_id' => $this->integer(),
            'type' => $this->string(10),
        ]);

        $this->createTable('crm_contact', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'type' => $this->string(10),
            'obfsNumber' => $this->string(11),
            'encryptedNumber' => $this->string(),
        ]);

        $this->createTable('crm_identity', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'birthDate' => $this->date(),
            'birthYear' => $this->string(4),
            'gender' => $this->string(1),
            'obfsName' => $this->string(20),
            'encryptedName' => $this->string(),
            'obfsIdNo' => $this->string(),
            'encryptedIdNo' => $this->string(),
        ]);

        $this->createTable('crm_activity', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'type' => $this->string(10),
            'summary' => $this->string(),
            'content' => $this->text(),
            'comment' => $this->string(),
        ]);

        $this->addColumn('user', 'crmAccount_id', $this->integer());
    }

    public function down()
    {
        echo "m170424_051738_crm_account cannot be reverted.\n";

        return false;
    }
}
