<?php

use yii\db\Migration;

class m160721_095211_update_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200114',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '待还款标的统计',
            'path' => 'product/productonline/hk-stats-count',
            'type' => '2',
            'auth_description' => '待还款标的统计',
            'status' => '1',
            'order_code' => '2',
        ]);

        $this->update('auth', [
            'level' => '2',
        ], ['sn' => 'P200200']);

        $this->update('auth', [
            'sn' => 'P200111',
        ], ['path' => 'order/onlinefangkuan/checkfk']);

        $this->update('auth', [
            'sn' => 'P200112',
        ], ['path' => 'repayment/repayment/dorepayment']);
    }

    public function down()
    {
        echo "m160721_095211_update_auth_table cannot be reverted.\n";

        return false;
    }
}
