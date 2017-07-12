<?php

use yii\db\Migration;

class m160922_115908_alter_user_asset_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'isTrading', $this->boolean()->notNull());
        $this->addColumn('user_asset', 'isTest', $this->boolean()->notNull());
    }

    public function down()
    {
        echo "m160922_115908_alter_user_asset_table cannot be reverted.\n";

        return false;
    }
}
