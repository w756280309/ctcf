<?php

use yii\db\Migration;

class m170213_013733_alter_affilicate extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'isRecommend', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170213_013733_alter_affilicate cannot be reverted.\n";

        return false;
    }
}
