<?php

use yii\db\Migration;

class m161202_060828_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100501',
            'psn' => 'A100500',
            'level' => '3',
            'auth_name' => '添加编辑分类',
            'path' => 'news/category/edit',
            'type' => '2',
            'auth_description' => '添加编辑分类',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100402',
            'psn' => 'A100400',
            'level' => '3',
            'auth_name' => '资讯图片上传',
            'path' => 'news/news/upload',
            'type' => '2',
            'auth_description' => '资讯图片上传',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161202_060828_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
