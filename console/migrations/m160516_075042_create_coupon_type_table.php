<?php

use yii\db\Migration;

class m160516_075042_create_coupon_type_table extends Migration
{
    public function up()
    {
        $this->createTable('coupon_type', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(20)->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'amount' => $this->decimal(6, 2)->notNull(),
            'minInvest' => $this->decimal(14, 2)->notNull(),
            'useStartDate' => $this->date(),
            'useEndDate' => $this->date(),
            'issueStartDate' => $this->date()->notNull(),
            'issueEndDate' => $this->date(),
            'isDisabled' => $this->boolean()->notNull(),
            'created_at' => $this->integer(10),
            'updated_at' => $this->integer(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('coupon_type');
    }
}
