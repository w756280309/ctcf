<?php

use yii\db\Migration;

class m170421_090116_alter_offline_loan_table extends Migration
{
    public function up()
    {
        $this->alterColumn('offline_loan' , 'yield_rate' , $this->string());
    }

    public function down()
    {
        echo "m170421_090116_alter_offline_loan_table cannot be reverted.\n";

        return false;
    }

}
