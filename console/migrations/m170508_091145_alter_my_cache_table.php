<?php

use yii\db\Migration;

class m170508_091145_alter_my_cache_table extends Migration
{
    public function up()
    {
        $this->createTable('cache_entry' , [
            'id' => $this->char(128),
            'expire' => $this->integer(11),
            'data' => "BLOB",
        ]);
        $this->addPrimaryKey('my_cache-id' , 'cache_entry' , 'id');
    }

    public function down()
    {
        echo "m170508_091145_alter_my_cache_table cannot be reverted.\n";

        return false;
    }

}
