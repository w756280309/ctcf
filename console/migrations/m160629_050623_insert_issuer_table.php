<?php

use yii\db\Migration;

class m160629_050623_insert_issuer_table extends Migration
{
    public function up()
    {
        $this->insert('issuer', [
            'name' => '立合旺通',
        ]);
    }

    public function down()
    {
        echo "m160629_050623_insert_issuer_table cannot be reverted.\n";

        return false;
    }
}