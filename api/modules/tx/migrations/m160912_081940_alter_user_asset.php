<?php

use yii\db\Migration;

class m160912_081940_alter_user_asset extends Migration
{

    public function up()
    {
        $this->addColumn('user_asset', 'asset_id', $this->integer());
        $this->addColumn('user_asset', 'tradeCount', $this->smallInteger());
    }

    public function down()
    {
        echo "m160912_081940_alter_user_asset cannot be reverted.\n";

        return false;
    }
}
