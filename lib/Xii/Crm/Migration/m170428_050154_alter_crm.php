<?php

use yii\db\Migration;

class m170428_050154_alter_crm extends Migration
{
    public function up()
    {
        $this->alterColumn('crm_contact', 'obfsNumber', $this->string());
        $this->addColumn('crm_engagement', 'type', $this->string());
        $this->alterColumn('crm_engagement', 'summary', $this->text());
        $this->alterColumn('crm_activity', 'summary', $this->text());
    }

    public function down()
    {
        echo "m170428_050154_alter_crm cannot be reverted.\n";

        return false;
    }
}
