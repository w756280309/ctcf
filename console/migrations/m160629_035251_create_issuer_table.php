<?php

use yii\db\Migration;

class m160629_035251_create_issuer_table extends Migration
{
    public function up()
    {
        $this->createTable('issuer', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull()->unique(),
        ]);
    }

    public function down()
    {
        $this->dropTable('issuer');
    }
}
