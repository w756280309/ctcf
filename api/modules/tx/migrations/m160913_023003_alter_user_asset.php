<?php

use yii\db\Migration;

class m160913_023003_alter_user_asset extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'maxTradableAmount', $this->decimal(14));
    }

    public function down()
    {
        echo "m160913_023003_alter_user_asset cannot be reverted.\n";

        return false;
    }
}
