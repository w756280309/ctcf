<?php

use yii\db\Migration;

class m161227_053212_add_online_product extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'isLicai', $this->boolean());
    }

    public function down()
    {
        echo "m161227_053212_add_online_product cannot be reverted.\n";
        return false;
    }
}
