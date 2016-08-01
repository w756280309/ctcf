<?php

use yii\db\Migration;

class m160727_064130_invite_record extends Migration
{
    public function up()
    {
        $this->createTable('invite_record', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'invitee_id' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    public function down()
    {
       $this->dropTable('invite_record');
    }
}
