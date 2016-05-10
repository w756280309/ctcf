<?php

use yii\db\Migration;

class m160510_170800_create_UserAffiliation_table extends Migration
{
    public function up()
    {
        $this->createTable('UserAffiliation', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'trackCode' => $this->string(),
            'affiliator_id' => $this->integer()
        ]);
    }

    public function down()
    {
        $this->dropTable('UserAffiliation');
    }
}
