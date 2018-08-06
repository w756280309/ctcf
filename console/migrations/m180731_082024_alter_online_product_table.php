<?php

use yii\db\Migration;

class m180731_082024_alter_online_product_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'original_borrower_id', $this->string(200)->comment('底层融资方ID;以,分隔'));
        $this->addColumn('online_product', 'check_status', $this->smallInteger(1)->defaultValue(0)->comment('审核状态:草稿0待审核1审核不通过2审核通过3'));
        $this->addColumn('online_product', 'check_remark', $this->string(500)->comment('审核备注'));
    }

    public function safeDown()
    {
        $this->dropColumn('online_product', 'original_borrower_id');
        $this->dropColumn('online_product', 'check_status');
        $this->dropColumn('online_product', 'check_remark');
    }
}
