<?php

use yii\db\Migration;

class m180106_042559_alter_callout_responder extends Migration
{
    public function safeUp()
    {
        $this->addColumn('callout_responder', 'promo_id', $this->integer()->null());
    }

    public function safeDown()
    {
        echo "m180106_042559_alter_callout_responder cannot be reverted.\n";

        return false;
    }
}
