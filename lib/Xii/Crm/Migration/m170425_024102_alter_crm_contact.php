<?php

use yii\db\Migration;

class m170425_024102_alter_crm_contact extends Migration
{
    public function up()
    {
        $this->alterColumn('crm_contact', 'obfsNumber', $this->string(20));
    }

    public function down()
    {
        echo "m170425_024102_alter_crm_contact cannot be reverted.\n";

        return false;
    }
}
