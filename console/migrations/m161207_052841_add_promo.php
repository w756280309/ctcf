<?php

use yii\db\Migration;

class m161207_052841_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', ['title' => '十二月邀请好友', 'startAt' => strtotime('2016-12-12 0:0:0'), 'endAt' => strtotime('2017-12-21 23:59:59'), 'key' => 'promo_invite_12', 'promoClass' => '\common\models\promo\PromoInvite12', 'whiteList' => '', 'isOnline' => false]);
    }

    public function down()
    {
        echo "m161207_052841_add_promo cannot be reverted.\n";

        return false;
    }
}
