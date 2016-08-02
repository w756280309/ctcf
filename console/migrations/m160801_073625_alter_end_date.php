<?php

use yii\db\Migration;

class m160801_073625_alter_end_date extends Migration
{
    public function up()
    {
        $this->update('coupon_type', ['issueEndDate' => '2016-09-25'], ['sn' => '0011:10000-30']);
        $this->update('coupon_type', ['issueEndDate' => '2016-09-25'], ['sn' => '0011:10000-50']);
        $this->update('promo', ['endAt' => strtotime('2016-09-25 23:59:59')], ['key' => 'WAP_INVITE_PROMO_160804']);
    }

    public function down()
    {
        echo "m160801_073625_alter_end_date cannot be reverted.\n";

        return false;
    }
}
