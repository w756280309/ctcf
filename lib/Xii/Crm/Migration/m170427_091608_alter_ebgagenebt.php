<?php

use yii\db\Migration;

class m170427_091608_alter_ebgagenebt extends Migration
{
    public function up()
    {
        $this->addColumn('crm_engagement', 'reception', $this->string()->comment('门店接待'));
    }

    public function down()
    {
        echo "m170427_091608_alter_ebgagenebt cannot be reverted.\n";

        return false;
    }
}
