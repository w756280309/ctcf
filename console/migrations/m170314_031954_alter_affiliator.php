<?php

use yii\db\Migration;

class m170314_031954_alter_affiliator extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'isO2O', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170314_031954_alter_affiliator cannot be reverted.\n";

        return false;
    }
}
