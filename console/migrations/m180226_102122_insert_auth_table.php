<?php

use yii\db\Migration;

/**
 * Class m180226_102122_insert_auth_table
 */
class m180226_102122_insert_auth_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100900',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '线下复投新增数据统计',
            'path' => 'datatj/user/offline-index',
            'type' => '1',
            'auth_description' => '线下复投新增数据统计',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180226_102122_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
