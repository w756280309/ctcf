<?php

use yii\db\Migration;

class m180305_084304_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'WX10104',
            'psn' => 'WX10000',
            'level' => '3',
            'auth_name' => '编辑公众号菜单',
            'path' => 'wechat/wechat-menu/index',
            'type' => '2',
            'auth_description' => '编辑微信公众号菜单',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->delete('auth',[
            'sn' => 'WX10100',
        ]);
        $this->delete('auth',[
            'sn' => 'WX10101',
        ]);
        $this->update('auth',
            ['psn' => 'WX10000'],
            ['sn' => 'WX10102']
        );
        $this->update('auth',
            ['psn' => 'WX10000'],
            ['sn' => 'WX10103']
        );
    }

    public function down()
    {
        echo "m180305_084304_insert_auth cannot be reverted.\n";

        return false;
    }
}
