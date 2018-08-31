<?php

use yii\db\Migration;

class m180824_062240_money_tranfer extends Migration
{
    public function safeUp()
    {
        $this->createTable('money_transfer', [
            'id' => $this->primaryKey(),
            'sn' => $this->string()->unique()->notNull()->comment('流水号'),
            'fromType' => $this->string(50)->notNull()->comment('转账方类型borrower,loan'),
            'fromId' => $this->string()->notNull()->comment('转账方标识'),
            'toType' => $this->string(50)->notNull()->comment('收款方类型borrower,loan,bank'),
            'toId' => $this->string()->notNull()->comment('收款方标识'),
            'amount' => $this->money(14, 2)->notNull()->comment('转账金额'),
            'status' => $this->string()->notNull()->defaultValue('init')->comment('初始init处理中pending成功success失败fail'),
            'retCode' => $this->string(50)->null()->comment('返回码'),
            'retMsg' => $this->string()->null()->comment('返回信息'),
            'remark' => $this->string()->null()->comment('转账备注'),
            'createTime' => $this->dateTime()->null()->comment('创建时间'),
            'updateTime' => $this->dateTime()->null()->comment('修改时间'),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('money_transfer');
    }
}
