<?php

use common\models\coupon\CouponType;
use yii\db\Migration;

class m160919_064033_insert_coupon_type_table extends Migration
{
    public function up()
    {
        $this->insert(CouponType::tableName(), [
            'sn' => '0016:1000-8',
            'name' => '注册奖励',
            'amount' => 8,
            'minInvest' => 1000,
            'issueStartDate' => '2016-09-26',
            'issueEndDate' => '2116-09-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);

        $this->insert(CouponType::tableName(), [
            'sn' => '0016:100000-80',
            'name' => '注册奖励',
            'amount' => 80,
            'minInvest' => 100000,
            'issueStartDate' => '2016-09-26',
            'issueEndDate' => '2116-09-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);

        $this->insert(CouponType::tableName(), [
            'sn' => '0016:200000-150',
            'name' => '注册奖励',
            'amount' => 150,
            'minInvest' => 200000,
            'issueStartDate' => '2016-09-26',
            'issueEndDate' => '2116-09-26',
            'isDisabled' => 0,
            'created_at' => time(),
            'updated_at' => time(),
            'expiresInDays' => 30,
            'isAudited' => 1,
        ]);
    }

    public function down()
    {
        echo "m160919_064033_insert_coupon_type_table cannot be reverted.\n";

        return false;
    }
}
