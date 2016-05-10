<?php

use yii\db\Migration;

class m160509_050909_bao_quan_queue extends Migration
{
    public function up()
    {
        $this->createTable('bao_quan_queue', [
            'id' => $this->primaryKey(),
            'proId' => $this->integer(11)->notNull()->unique(),
            'status' => $this->integer(1)->defaultValue(1),
            'created_at' => $this->integer(10),
            'updated_at' => $this->integer(10),
        ]);
    }

    public function down()
    {
        $this->dropTable('bao_quan_queue');
    }
}
