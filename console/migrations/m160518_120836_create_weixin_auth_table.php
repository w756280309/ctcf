<?php

use yii\db\Migration;

class m160518_120836_create_weixin_auth_table extends Migration
{
    public function up()
    {
        $this->createTable('weixin_auth', [
            'id' => $this->primaryKey(),
            'appId' => $this->string()->notNull()->unique(),
            'accessToken' => $this->string(),
            'jsApiTicket' => $this->string(),
            'expiresAt' => $this->integer(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('weixin_auth');
    }
}
