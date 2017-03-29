<?php

use yii\db\Migration;

class m170314_013147_captcha extends Migration
{
    public function up()
    {
        $this->createTable('captcha', [
            'id' => $this->string()->notNull()->unique(),
            'code' => $this->string()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'expireTime' => $this->dateTime()->notNull(),
        ]);
        $this->addPrimaryKey('pk', 'captcha', 'id');
    }

    public function down()
    {
        echo "m170314_013147_captcha cannot be reverted.\n";

        return false;
    }
}
