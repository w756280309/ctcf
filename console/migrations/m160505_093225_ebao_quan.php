<?php

use yii\db\Migration;

class m160505_093225_ebao_quan extends Migration
{
    public function up()
    {
        $this->createTable('ebao_quan', [
            'id' => $this->primaryKey(),
            'type' => $this->integer(1),//合同类型
            'title' => $this->string(200),
            'orderId' => $this->integer(10)->notNull(),//订单id
            'uid' => $this->integer(10)->notNull(),//用户id
            'baoId' => $this->integer(10)->notNull(),//保全id
            'docHash' => $this->string(200),//保全返回hash
            'preservationTime' => $this->string(13),//保全时间抽,13位
            'success' => $this->boolean(),//返回状态
            'errMessage' => $this->string(200),//失败信息
            'created_at' => $this->integer(10),
            'updated_at' => $this->integer(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('ebao_quan');
    }
}
