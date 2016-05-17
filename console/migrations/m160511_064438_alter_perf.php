<?php

use yii\db\Migration;

class m160511_064438_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'successFound', $this->integer());//融资项目
        $this->addColumn('perf', 'rechargeMoney', $this->decimal());//充值金额
        $this->addColumn('perf', 'rechargeCost', $this->decimal());//充值手续费
        $this->addColumn('perf', 'draw', $this->decimal());//提现
        $this->addColumn('perf', 'created_at', $this->integer(10));//统计时间
    }

    public function down()
    {
        echo "m160511_064438_alter_perf cannot be reverted.\n";

        return false;
    }
}
