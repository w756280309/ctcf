<?php

use yii\db\Migration;

class m180201_091334_alter_share_log_table extends Migration
{
    public function Up()
    {
        $this->renameColumn('share_log', 'uid', 'userId');
    }

    public function Down()
    {
        echo "m180201_091334_alter_share_log_table cannot be reverted.\n";

        return false;
    }
}
