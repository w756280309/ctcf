<?php

use yii\db\Migration;

class m160701_012547_update_auth_table extends Migration
{
    public function up()
    {
        $this->update('auth', [
            'sn' => 'D100201',
            'psn' => 'D100200',
            'order_code' => 5,
        ], ['path' => 'datatj/datatj/list']);

        $this->update('auth', [
            'sn' => 'D100400',
        ], ['path' => 'datatj/issuer/list?id=1']);

        $this->update('auth', [
            'sn' => 'D100401',
            'psn' => 'D100400',
            'type' => '2',
        ], ['path' => 'datatj/issuer/export']);
    }

    public function down()
    {
        echo "m160701_012547_update_auth_table cannot be reverted.\n";

        return false;
    }
}
