<?php

use yii\db\Migration;

class m180124_114308_alter_code_table extends Migration
{
    public function Up()
    {
        $this->createIndex('goodsType_sn', 'code', 'goodsType_sn');
    }

    public function Down()
    {
        echo "m180124_114308_alter_code_table cannot be reverted.\n";

        return false;
    }
}
