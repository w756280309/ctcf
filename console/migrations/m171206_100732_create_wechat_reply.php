<?php

use yii\db\Migration;

class m171206_100732_create_wechat_reply extends Migration
{
    public function up()
    {
        $this->createTable('wechat_reply', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->string()->notNull()->comment('回复类型'),
            'keyword' => $this->string()->notNull()->comment('关键字'),
            'content' => $this->string()->notNull()->comment('内容'),
            'isDel' => $this->boolean()->notNull()->defaultValue(0)->comment('是否删除'),
            'createdAt' => $this->integer()->notNull()->comment('创建时间'),
            'updatedAt' => $this->integer()->notNull()->comment('更新时间'),
        ]);
        //权限
        $this->insert('auth', [
            'sn' => 'WX10000',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '公众号管理',
            'path' => 'wechat/reply/index',
            'type' => '2',
            'auth_description' => '微信公众号管理',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'WX10100',
            'psn' => 'WX10000',
            'level' => '3',
            'auth_name' => '自动回复',
            'path' => 'wechat/reply/index',
            'type' => '2',
            'auth_description' => '公众号自动回复管理',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'WX10101',
            'psn' => 'WX10100',
            'level' => '4',
            'auth_name' => '列表',
            'path' => 'wechat/reply/index',
            'type' => '2',
            'auth_description' => '自动回复列表',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'WX10102',
            'psn' => 'WX10100',
            'level' => '4',
            'auth_name' => '编辑',
            'path' => 'wechat/reply/edit',
            'type' => '2',
            'auth_description' => '编辑',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m171206_100732_create_wechat_reply cannot be reverted.\n";

        return false;
    }
}
