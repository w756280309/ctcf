<?php

use yii\db\Migration;

class m161123_064657_insert_share_table extends Migration
{
    public function up()
    {
        $this->insert('share', [
            'shareKey' => 'h5',
            'title' => '温都金服 - 温都报业传媒旗下理财平台',
            'description' => '市民身边的财富管家',
            'imgUrl' => 'https://static.wenjf.com/m/images/wechat/h5.png',
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m161123_064657_insert_share_table cannot be reverted.\n";

        return false;
    }
}
