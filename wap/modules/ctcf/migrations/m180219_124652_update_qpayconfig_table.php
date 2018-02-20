<?php

use yii\db\Migration;

/**
 * Class m180219_124652_update_qpayconfig_table
 */
class m180219_124652_update_qpayconfig_table extends Migration
{
    /**
     * @inheritdoc
     * 取消m端和pc端对于招商银行和民生银行的快捷绑卡
     */
    public function up()
    {
        $bankId = $this->getDb()->createCommand('select id from bank where bankName="招商银行"')->queryScalar();
        $this->update('qpayconfig', ['isDisabled' => 1], ['bankId'=> $bankId]);
        $bankId = $this->getDb()->createCommand('select id from bank where bankName="民生银行"')->queryScalar();
        $this->update('qpayconfig', ['isDisabled' => 1], ['bankId'=> $bankId]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180219_124652_update_qpayconfig_table cannot be reverted.\n";

        return false;
    }
}
