<?php

use yii\db\Migration;

class m161201_064355_third_party_connect extends Migration
{
    public function up()
    {
        $this->createTable('third_party_connect', [
           'id' => $this->primaryKey(),
            'publicId' => $this->string(255)->notNull(),
            'visitor_id' => $this->string(255),
            'user_id' => $this->integer()->notNull(),
            'thirdPartyUser_id' => $this->string(255),
            'createTime' => $this->string(),
        ]);
    }

    public function down()
    {
       $this->dropTable('third_party_connect');
    }
}
