<?php

use yii\db\Migration;

class m170502_061402_crm extends Migration
{
    public function up()
    {
        #crm_activity
        $this->dropColumn('crm_activity', 'type');
        $this->dropColumn('crm_activity', 'summary');
        $this->dropColumn('crm_activity', 'content');
        $this->dropColumn('crm_activity', 'comment');

        $this->addColumn('crm_activity', 'ref_type', $this->string());
        $this->addColumn('crm_activity', 'ref_id', $this->string());

        #crm_phone_call
        $this->renameTable('crm_engagement', 'crm_phone_call');
        $this->dropColumn('crm_phone_call', 'type');
        $this->renameColumn('crm_phone_call', 'summary', 'comment');

        #crm_branch_visit
        $this->createTable('crm_branch_visit', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'visitDate' => $this->date(),
            'recp_name' => $this->string(),
            'content' => $this->text(),
            'comment' => $this->text(),
        ]);

        #crm_note
        $this->createTable('crm_note', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'creator_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
            'content' => $this->text(),
        ]);


    }

    public function down()
    {
        echo "m170502_061402_crm cannot be reverted.\n";

        return false;
    }
}
