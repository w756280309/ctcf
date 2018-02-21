<?php

use yii\db\Migration;

class m180210_054058_alter_session extends Migration
{
    public function safeUp()
    {
        $this->addColumn('session',  'answers', $this->text()->null()->comment('答题信息记录'));
    }

    public function safeDown()
    {
        echo "m180210_054058_alter_session cannot be reverted.\n";

        return false;
    }
}
