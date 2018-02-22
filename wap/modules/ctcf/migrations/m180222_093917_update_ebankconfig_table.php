<?php

use yii\db\Migration;

/**
 * Class m180222_093917_update_ebankconfig_table
 */
class m180222_093917_update_ebankconfig_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $bankId = $this->getDb()->createCommand('select id from bank where bankName="招商银行"')->queryScalar();
        $this->update('ebankconfig', ['typePersonal' => 1, 'typeBusiness' => 1], ['bankId'=> $bankId]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180222_093917_update_ebankconfig_table cannot be reverted.\n";

        return false;
    }
}
