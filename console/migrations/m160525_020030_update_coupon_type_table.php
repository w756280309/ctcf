<?php

use yii\db\Migration;

class m160525_020030_update_coupon_type_table extends Migration
{
    public function up()
    {
        $this->addColumn('coupon_type', 'expiresInDays', $this->integer(3));
        $this->addColumn('coupon_type', 'customerType', $this->integer(1));
        $this->addColumn('coupon_type', 'loanCategories', $this->string(30));
        $this->addColumn('coupon_type', 'allowCollect', $this->boolean());
        $this->addColumn('coupon_type', 'isAudited', $this->boolean()->notNull());
        $this->alterColumn('coupon_type', 'issueEndDate', $this->date()->notNull());
    }

    public function down()
    {
        $this->dropColumn('coupon_type', 'expiresInDays');
        $this->dropColumn('coupon_type', 'customerType');
        $this->dropColumn('coupon_type', 'loanCategories');
        $this->dropColumn('coupon_type', 'allowCollect');
        $this->dropColumn('coupon_type', 'isAudited');
        $this->alterColumn('coupon_type', 'issueEndDate', $this->date());
    }
}
