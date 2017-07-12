<?php

use yii\db\Migration;

class m160927_053212_alter_user_asset_table extends Migration
{
    public function up()
    {
        $this->addColumn('user_asset', 'note_id', $this->integer());
    }

    public function down()
    {
        echo "m160927_053212_alter_user_asset_table cannot be reverted.\n";

        return false;
    }
}
