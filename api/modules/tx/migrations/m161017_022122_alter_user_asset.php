<?php

use yii\db\Migration;

class m161017_022122_alter_user_asset extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'credit_order_id', $this->integer());
    }

    public function down()
    {
        echo "m161017_022122_alter_user_asset cannot be reverted.\n";

        return false;
    }
}
