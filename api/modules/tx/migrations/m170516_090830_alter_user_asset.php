<?php

use yii\db\Migration;

class m170516_090830_alter_user_asset extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'allowTransfer', $this->boolean()->defaultValue(true));
    }

    public function down()
    {
        echo "m170516_090830_alter_user_asset cannot be reverted.\n";

        return false;
    }
}
