<?php

use yii\db\Migration;

class m161229_052632_alter_coupon_code extends Migration
{
    public function up()
    {
        $this->renameTable('coupon_code', 'code');
        $this->addColumn('code', 'goodsType', $this->smallInteger());
        $this->renameColumn('code', 'couponType_sn', 'goodsType_sn');

        //更新当前goodsType
        $this->update('code', ['goodsType' => 1]);
    }

    public function down()
    {
        echo "m161229_052632_alter_coupon_code cannot be reverted.\n";

        return false;
    }
}
