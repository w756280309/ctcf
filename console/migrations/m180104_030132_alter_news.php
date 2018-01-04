<?php

use yii\db\Migration;

class m180104_030132_alter_news extends Migration
{
    public function up()
    {
        $this->addColumn('news', 'investLeast', $this->decimal(14, 2)->notNull()->defaultValue(0)->comment('最低投资可见'));
    }

    public function down()
    {
        echo "m180104_030132_alter_news cannot be reverted.\n";

        return false;
    }
}
