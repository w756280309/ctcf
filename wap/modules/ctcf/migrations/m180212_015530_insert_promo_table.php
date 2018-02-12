<?php

use yii\db\Migration;

class m180212_015530_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '购买标的送积分',
            'key' => 'loan_order_points',
            'promoClass' => 'common\models\promo\LoanOrderPoints',
            'whiteList' => '',
            'isOnline' => '1',
            'startTime' => '2018-01-01 00:00:00',
            'endTime' => '',
            'config' => '',
            'isO2O' => '0',
        ]);
    }

    public function down()
    {
        echo "m180212_015530_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
