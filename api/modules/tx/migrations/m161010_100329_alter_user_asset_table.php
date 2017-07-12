<?php

use yii\db\Migration;

class m161010_100329_alter_user_asset_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'isInvalid', $this->boolean()->notNull());
    }

    public function down()
    {
        echo "m161010_100329_alter_user_asset_table cannot be reverted.\n";

        return false;
    }
}
