<?php

use yii\db\Migration;

class m170307_081121_insert_coupon_type_table extends Migration
{
    public function up()
    {
        $this->insert('coupon_type', [
            'sn' => '0023:10000-10',
            'name' => 'APP投资红包',
            'amount' => 10,
            'minInvest' => 10000,
            'issueStartDate' => date('Y-m-d'),
            'issueEndDate' => date('Y-m-d', strtotime('+ 1 year')),
            'isDisabled' => false,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 15,
            'isAudited' => 1,
            'isAppOnly' => true,
        ]);
    }

    public function down()
    {
        echo "m170307_081121_insert_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
