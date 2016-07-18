<?php

use yii\db\Migration;

//用户信息表
class m160718_060959_user_info extends Migration
{
    public function up()
    {
        $this->createTable('user_info', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unique(),//用户ID
            'isInvested' => $this->integer(1)->defaultValue(0),//是否投资过
            'investCount' => $this->integer(5)->defaultValue(0),//成功投资次数
            'investTotal' => $this->decimal(14, 2),//累计投资金额
            'firstInvestDate' => $this->date(),//第一次投资时间 Y-m-d
            'lastInvestDate' => $this->date(),//最后一次投资时间 Y-m-d
            'firstInvestAmount' => $this->decimal(14, 2),//第一次投资金额
            'lastInvestAmount' => $this->decimal(14, 2),//最后一次投资金额
            'averageInvestAmount' => $this->decimal(14, 2),//平均投资金额
        ]);
    }

    public function down()
    {
        $this->dropTable('user_info');
    }
}
