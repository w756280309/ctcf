<?php

use yii\db\Migration;

class m170518_032832_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '第二期0元夺宝',
            'startTime' => '2017-05-22 00:00:00',
            'endTime' => '2017-05-27 23:59:59',
            'key' => 'duo_bao_0522',
            'promoClass' => 'common\models\promo\DuoBao',
            'isOnline' => false,
            'config' => json_encode([
                'image' => 'https://static.wenjf.com/upload/link/link1493968060976889.png',
            ]),
        ]);
    }

    public function down()
    {
        echo "m170518_032832_add_promo cannot be reverted.\n";

        return false;
    }
}
