<?php

use yii\db\Migration;

class m170324_093356_alter_user_info extends Migration
{
    public function up()
    {
        $this->addColumn('user_info', 'isAffiliator', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170324_093356_alter_user_info cannot be reverted.\n";

        return false;
    }
}
