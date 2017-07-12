<?php

use yii\db\Migration;

class m160921_112551_alter_credit_order_transfers extends Migration
{
    public function up()
    {
        $this->addColumn('credit_order', 'buyerPaymentStatus', $this->smallInteger()->notNull());
        $this->addColumn('credit_order', 'sellerRefundStatus', $this->smallInteger()->notNull());
        $this->addColumn('credit_order', 'feeTransferStatus', $this->smallInteger()->notNull());
    }

    public function down()
    {
        echo "m160921_112551_alter_credit_order_transfers cannot be reverted.\n";

        return false;
    }
}
