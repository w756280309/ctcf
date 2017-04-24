<?php

use yii\db\Migration;

class m170424_064018_alter_crm extends Migration
{
    public function up()
    {
        $this->addColumn('crm_account', 'idConverted', $this->boolean());
        $this->createIndex('unique_account_id', 'user', 'crmAccount_id', true);
    }

    public function down()
    {
        echo "m170424_064018_alter_crm cannot be reverted.\n";

        return false;
    }
}
