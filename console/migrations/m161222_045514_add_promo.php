<?php

use yii\db\Migration;

class m161222_045514_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', ['title' => '圣诞砸金蛋', 'startAt' => strtotime('2016-12-24 0:0:0'), 'endAt' => strtotime('2016-12-26 23:59:59'), 'key' => 'promo_golden_egg', 'promoClass' => '\common\models\promo\GoldenEgg', 'whiteList' => '', 'isOnline' => false]);
    }

    public function down()
    {
        echo "m161222_045514_add_promo cannot be reverted.\n";

        return false;
    }
}
