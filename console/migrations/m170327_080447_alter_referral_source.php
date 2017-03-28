<?php

use yii\db\Migration;

class m170327_080447_alter_referral_source extends Migration
{
    public function up()
    {
        $this->dropColumn('referral_source', 'source');
        $this->dropColumn('referral_source', 'isO2O');
    }

    public function down()
    {
        echo "m170327_080447_alter_referral_source cannot be reverted.\n";

        return false;
    }
}
