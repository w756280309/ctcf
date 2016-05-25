<?php

use yii\db\Migration;

class m160524_092322_update_afiliator_table extends Migration
{
    public function up()
    {
        $this->alterColumn('affiliator', 'name', $this->string()->unique());
    }

    public function down()
    {
        $this->alterColumn('affiliator', 'name', $this->string());
    }
}
