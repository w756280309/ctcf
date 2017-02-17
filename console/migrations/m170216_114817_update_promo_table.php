<?php

use yii\db\Migration;

class m170216_114817_update_promo_table extends Migration
{
    public function up()
    {
        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1487245419910160.png',
            'rules' => [
                '注册送288元红包；',
                '2017年1月18日起首次投资，送沃尔玛70元超市卡；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];

        $this->update('promo', [
            'startTime' => '2017-02-17 00:00:00',
            'config' => json_encode($config),
        ], [
            'key' => 'wrm170210',
        ]);
    }

    public function down()
    {
        echo "m170216_114817_update_promo_table cannot be reverted.\n";

        return false;
    }
}
