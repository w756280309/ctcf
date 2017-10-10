<?php

use yii\db\Migration;

class m170928_104755_alter_ebao_quan extends Migration
{
    public function Up()
    {
        $this->alterColumn('ebao_quan', 'baoId', $this->string(32));
    }

    public function Down()
    {
        echo "m170928_104755_alter_ebao_quan cannot be reverted.\n";

        return false;
    }
}
