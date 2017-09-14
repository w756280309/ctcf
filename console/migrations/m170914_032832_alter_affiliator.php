<?php

use yii\db\Migration;

class m170914_032832_alter_affiliator extends Migration
{
    public function safeUp()
    {
        $this->addColumn('affiliator', 'parentId', $this->integer()->comment('父级分销商ID'));
    }

    public function safeDown()
    {
        echo "m170914_032832_alter_affiliator cannot be reverted.\n";

        return false;
    }
}
