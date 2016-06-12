<?php

use yii\db\Migration;

class m160612_053750_drop_user_bank_status extends Migration
{
    public function up()
    {
        $this->dropColumn('user_bank', 'status');
    }

    public function down()
    {
        echo "m160612_053750_drop_user_bank_status cannot be reverted.\n";

        return false;
    }
}
