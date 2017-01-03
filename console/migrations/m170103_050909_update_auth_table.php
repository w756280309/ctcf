<?php

use yii\db\Migration;

class m170103_050909_update_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'path' => 'repayment/repayment/index',
        ], ['path' => 'repayment/repayment']);
    }

    public function down()
    {
        echo "m170103_050909_update_auth_table cannot be reverted.\n";

        return false;
    }
}
