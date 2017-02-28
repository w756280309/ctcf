<?php

use yii\db\Migration;

class m170227_080848_add_promo extends Migration
{
    public function up()
    {
        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1488182799191824.png',
            'rules' => [
                '注册送288元红包；',
                '注册赢好礼，xxx等你拿；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];
        $this->insert('promo', [
            'title' => 'O2O-test',
            'startTime' => date('Y-m-d'),
            'endTime' => '',
            'key' => 'o2o0301',
            'promoClass' => '',
            'isOnline' => true,
            'config' => json_encode($config),
        ]);
    }

    public function down()
    {
        echo "m170227_080848_add_promo cannot be reverted.\n";

        return false;
    }
}
