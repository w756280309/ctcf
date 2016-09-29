<?php

use yii\db\Migration;

class m160929_082209_delete_user_asset_table extends Migration
{
    public function up()
    {
        $this->dropTable('user_asset');
    }

    public function down()
    {
        echo "m160929_082209_delete_user_asset_table cannot be reverted.\n";

        return false;
    }
}
