<?php

use yii\db\Migration;

class m170227_032639_alter_referral_source extends Migration
{
    public function up()
    {
        $this->addColumn('referral_source', 'source', $this->string(50));
        $this->addColumn('referral_source', 'isO2O', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170227_032639_alter_referral_source cannot be reverted.\n";

        return false;
    }
}
