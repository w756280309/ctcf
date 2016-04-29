<?php

use yii\db\Migration;

class m160428_071753_create_perf_table extends Migration
{
    public function up()
    {
        $this->createTable('perf', [
            'id' => $this->primaryKey(),
            'bizDate' => $this->date(),
            'uv' => $this->integer(),
            'pv' => $this->integer(),
            'bounceRate' => $this->float(),
            'reg' => $this->integer(),
            'regConv' => $this->float(),
            'idVerified' => $this->integer(),
            'qpayEnabled' => $this->integer(),
            'investor' => $this->integer(),
            'newInvestor' => $this->integer(),
            'chargeViaPos' => $this->decimal(),
            'chargeViaEpay' => $this->decimal(),
            'drawAmount' => $this->decimal(),
            'investmentInWyj' => $this->decimal(),
            'investmentInWyb' => $this->decimal(),
            'totalInvestment' => $this->decimal(),
        ]);
    }

    public function down()
    {
        $this->dropTable('perf');
    }
}
