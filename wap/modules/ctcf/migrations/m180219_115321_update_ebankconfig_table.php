<?php

use yii\db\Migration;

/**
 * Class m180219_115321_update_ebankconfig_table
 */
class m180219_115321_update_ebankconfig_table extends Migration
{
    /**
     * @inheritdoc
     * 取消PC端招商银行网银充值显示
     */
    public function up()
    {
        $bankId = $this->getDb()->createCommand('select id from bank where bankName="招商银行"')->queryScalar();
        $this->update('ebankconfig', ['typePersonal' => 0, 'typeBusiness' => 0], ['bankId'=> $bankId]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180219_115321_update_ebankconfig_table cannot be reverted.\n";

        return false;
    }
}
