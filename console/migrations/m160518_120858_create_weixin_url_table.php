<?php

use yii\db\Migration;

class m160518_120858_create_weixin_url_table extends Migration
{
    public function up()
    {
        $this->createTable('weixin_url', [
            'id' => $this->primaryKey(),
            'auth_id' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('weixin_url');
    }
}
