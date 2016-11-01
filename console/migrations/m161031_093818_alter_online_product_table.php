<?php

use yii\db\Migration;

class m161031_093818_alter_online_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'allowUseCoupon', $this->boolean()->notNull());
        $this->update('online_product', ['allowUseCoupon' => true]);
    }

    public function down()
    {
        $this->dropColumn('online_product', 'allowUseCoupon');
    }
}
