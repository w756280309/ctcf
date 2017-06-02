<?php

use yii\db\Migration;

class m170602_095548_alter_callout extends Migration
{
    public function up()
    {
        $this->createIndex('unique_key', 'callout', ['promo_id', 'user_id'], true);
    }

    public function down()
    {
        echo "m170602_095548_alter_callout cannot be reverted.\n";

        return false;
    }
}
