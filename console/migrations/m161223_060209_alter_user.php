<?php

use yii\db\Migration;

class m161223_060209_alter_user extends Migration
{
    public function up()
    {
        $this->update('user', ['points' => 0, 'coins' => 0]);
        $this->alterColumn('user', 'points', $this->integer()->defaultValue(0));
        $this->alterColumn('user', 'coins', $this->integer()->defaultValue(0));
    }

    public function down()
    {
        echo "m161223_060209_alter_user cannot be reverted.\n";

        return false;
    }
}
