<?php

use yii\db\Migration;

class m161220_025019_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
           'title' => '生日当天发放代金券',
            'startAt' => strtotime('2016-12-30 00:00:00'),
            'endAt' => strtotime('2020-11-15 23:59:59'),
            'key' => 'promo_birthday_coupon',
            'promoClass' => \common\models\promo\BirthdayCoupon::class,
        ]);
    }

    public function down()
    {
        echo "m161220_025019_add_promo cannot be reverted.\n";

        return false;
    }
}
