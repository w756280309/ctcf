<?php

use yii\db\Migration;

class m160830_075734_alter_online_product_table extends Migration
{
    public function up()
    {
        $this->dropColumn('online_product', 'graceDays');
    }

    public function down()
    {
        $this->addColumn('online_product', 'graceDays', $this->integer(11)->notNull());

        return false;
    }
}
