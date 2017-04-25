<?php

use yii\db\Migration;

class m170418_025645_alter_offline_loan_table extends Migration
{
    public function up()
    {
        $this->addColumn('offline_loan' , 'sn' , $this->char(32));
        $this->addColumn('offline_loan' , 'yield_rate' , $this->decimal(6,4));
        $this->addColumn('offline_loan' , 'jixi_time' , $this->dateTime());
        $this->addColumn('offline_loan' , 'finish_date' , $this->dateTime());
    }

    public function down()
    {
        echo "m170418_025645_alter_offline_loan_table cannot be reverted.\n";

        return false;
    }

}
