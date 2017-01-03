<?php

use yii\db\Migration;

class m170103_060920_alter_loadn extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'pointsMultiple', $this->smallInteger()->defaultValue(1));
    }

    public function down()
    {
        echo "m170103_060920_alter_loadn cannot be reverted.\n";

        return false;
    }
}
