<?php

use yii\db\Migration;

class m170504_110248_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '新老用户0元夺宝',
            'startTime' => '2017-05-06 00:00:00',
            'endTime' => '2017-05-15 23:59:59',
            'key' => 'duobao0504',
            'promoClass' => 'common\models\promo\DuoBao',
            'isOnline' => false,
            'config' => json_encode([
                'image' => 'https://static.wenjf.com/upload/link/link1493968060976889.png',
            ]),
        ]);
    }

    public function down()
    {
        echo "m170504_110248_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
