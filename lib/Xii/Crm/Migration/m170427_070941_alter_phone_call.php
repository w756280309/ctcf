<?php

use yii\db\Migration;

class m170427_070941_alter_phone_call extends Migration
{
    public function up()
    {
        $this->renameTable('crm_phone_call', 'crm_engagement');
        $this->addColumn('crm_engagement', 'summary', $this->string());
    }

    public function down()
    {
        echo "m170427_070941_alter_phone_call cannot be reverted.\n";

        return false;
    }
}
