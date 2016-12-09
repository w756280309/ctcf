<?php

use yii\db\Migration;

class m161205_094615_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', ['title' => '幸运双十二，疯抢Ipad', 'startAt' => strtotime('2016-12-12 0:0:0'), 'endAt' => strtotime('2016-12-21 23:59:59'), 'key' => 'promo_12_12_21', 'promoClass' => '\common\models\promo\Promo1212', 'whiteList' => '', 'isOnline' => false]);
    }

    public function down()
    {
        echo "m161205_094615_add_promo cannot be reverted.\n";

        return false;
    }
}
