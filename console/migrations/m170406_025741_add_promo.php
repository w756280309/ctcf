<?php

use yii\db\Migration;

class m170406_025741_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '15亿砸金蛋',
            'key' => 'promo_070410_egg',
            'promoClass' => 'common\models\promo\PromoEgg',
            'isOnline' => true,
            'startTime' => '2017-04-10 00:00:00',
            'endTime' => '2017-04-12 23:59:59',
        ]);
    }

    public function down()
    {
        echo "m170406_025741_add_promo cannot be reverted.\n";

        return false;
    }
}
