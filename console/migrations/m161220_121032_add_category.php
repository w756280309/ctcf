<?php

use yii\db\Migration;

class m161220_121032_add_category extends Migration
{
    public function up()
    {
        $this->insert('category', [
            'name' => '理财指南',
            'key' => 'licai',
            'parent_id' => '0',
            'level' => '1',
            'description' => '理财指南',
            'sort' => '1',
            'status' => '1',
            'type' => '1',
        ]);

        $this->insert('category', [
            'name' => '投资技巧',
            'key' => 'touzi',
            'parent_id' => '0',
            'level' => '1',
            'description' => '投资技巧',
            'sort' => '1',
            'status' => '1',
            'type' => '1',
        ]);

    }

    public function down()
    {
        echo "m161220_121032_add_category cannot be reverted.\n";

        return false;
    }
}
