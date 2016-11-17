<?php

use yii\db\Migration;

class m161117_014719_share extends Migration
{
    public function up()
    {
        $this->createTable('share', [
            'id' => $this->primaryKey(),
            'shareKey' => $this->string(20)->unique(),
            'title' => $this->string(200),
            'description' => $this->text(),
            'imgUrl' => $this->string(100),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    public function down()
    {
        $this->dropTable('share');
    }
}
