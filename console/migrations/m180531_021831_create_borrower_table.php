<?php

use yii\db\Migration;

class m180531_021831_create_borrower_table extends Migration
{
    public function up()
    {
        $this->createTable('borrower', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull()->defaultValue(0)->comment('融资会员ID'),
            'allowDisbursement' => $this->boolean()->notNull()->defaultValue(0)->comment('能否作为放款方'),
            'type' => $this->smallInteger()->notNull()->defaultValue(1)->comment('会员类型 1企业融资方 2个人融资方 3用款方 4代偿方 5担保方'),
            'created_at' => $this->integer()->notNull()->defaultValue(0)->comment('创建时间'),
            'updated_at' => $this->integer()->notNull()->defaultValue(0)->comment('更新时间'),
        ]);

    }

    public function down()
    {
        echo "m180531_021831_create_borrower_table cannot be reverted.\n";

        return false;
    }
}
