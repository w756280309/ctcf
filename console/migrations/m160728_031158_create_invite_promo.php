<?php

use yii\db\Migration;

class m160728_031158_create_invite_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => 'WAP邀请好友',
            'key' => 'WAP_INVITE_PROMO_160804',
            'startAt' => strtotime('2016-08-04 00:00:00'),
            'endAt' => strtotime('2016-09-04 23:59:59')//活动结束时间待定
        ]);
    }

    public function down()
    {
        echo "m160728_031158_create_invite_promo cannot be reverted.\n";

        return false;
    }
}
