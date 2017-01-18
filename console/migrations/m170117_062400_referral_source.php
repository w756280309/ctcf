<?php

use yii\db\Migration;

class m170117_062400_referral_source extends Migration
{
    public function up()
    {
        $this->createTable('referral_source', [
           'id' => $this->primaryKey(),
            'key' => $this->string(15)->notNull()->unique(),
            'target' => $this->string()->notNull(),
            'title' => $this->string(),
            'description' => $this->string(),
            'isActive' => $this->boolean()->defaultValue(true),
            'createTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        echo "m170117_062400_referral_source cannot be reverted.\n";

        return false;
    }
}
