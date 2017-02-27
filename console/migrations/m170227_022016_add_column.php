<?php

use yii\db\Migration;

class m170227_022016_add_column extends Migration
{
    public function up()
    {
        $this->addColumn('sms', 'safeMobile', $this->string());
        $this->addColumn('sms_message', 'safeMobile', $this->string());
    }

    public function down()
    {
        echo "m170227_022016_add_column cannot be reverted.\n";

        return false;
    }
}
