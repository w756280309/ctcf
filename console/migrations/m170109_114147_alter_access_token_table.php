<?php

use yii\db\Migration;

class m170109_114147_alter_access_token_table extends Migration
{
    public function up()
    {
        $this->addColumn('AccessToken', 'updateTime', $this->dateTime());
    }

    public function down()
    {
        echo "m170109_114147_alter_access_token_table cannot be reverted.\n";

        return false;
    }
}
