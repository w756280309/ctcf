<?php

use yii\db\Migration;

class m170221_075759_create_jx_page extends Migration
{
    public function up()
    {
        $this->createTable('jx_page', [
            'id' => $this->primaryKey(),
            'issuerId' => $this->integer()->unsigned()->unique()->notNull(),
            'title' => $this->string(100)->notNull(),
            'content' => $this->text()->notNull(),
            'createTime' => $this->dateTime(),
            'admin_id' => $this->integer(),
        ]);
    }

    public function down()
    {
        echo "m170221_075759_create_jx_page cannot be reverted.\n";

        return false;
    }
}
