<?php

use yii\db\Migration;

class m170419_055255_alter_bao_quan_queue extends Migration
{
    public function up()
    {
        $this->createIndex('unique_item', 'bao_quan_queue', ['itemId', 'itemType'], true);
    }

    public function down()
    {
        echo "m170419_055255_alter_bao_quan_queue cannot be reverted.\n";

        return false;
    }
}
