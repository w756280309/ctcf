<?php

use yii\db\Migration;

class m160503_123142_create_BankCardUpdate_table extends Migration
{
    public function up()
    {
        $this->createTable('bank_card_update', [
            'id' => $this->primaryKey(10),
            'sn' => $this->string(32)->notNull()->unique(),
            'oldSn' => $this->string(32)->notNull(),
            'uid' => $this->integer(10)->notNull(),
            'epayUserId' => $this->string(60)->notNull(),
            'bankId' => $this->string(30)->notNull(),
            'bankName' => $this->string()->notNull(),
            'cardHolder' => $this->string(30)->notNull(),
            'cardNo' => $this->string(50)->notNull(),
            'status' => $this->integer(1)->notNull()->defaultValue(0),
            'created_at' => $this->integer(10),
            'updated_at' => $this->integer(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('bank_card_update');
    }
}
