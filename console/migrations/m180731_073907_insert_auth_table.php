<?php

use yii\db\Migration;

/**
 * Class m180731_073907_insert_auth_table
 */
class m180731_073907_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200303',
            'psn' => 'P200300',
            'level' => '3',
            'auth_name' => '下载标的转让合同',
            'path' => 'product/productonline/miit-baoquan',
            'type' => '2',
            'auth_description' => '重新下载标的转让合同',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        $this->delete('auth', ['sn' => 'P200303']);

        return true;
    }
}
