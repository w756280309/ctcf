<?php

use yii\db\Migration;

class m160629_052236_insert_online_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'issuer', $this->integer());
        $this->addColumn('online_product', 'issuerSn', $this->string(30));
    }

    public function down()
    {
        $this->dropColumn('online_product', 'issuer');
        $this->dropColumn('online_product', 'issuerSn');
    }
}
