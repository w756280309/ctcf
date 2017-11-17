<?php

use yii\db\Migration;

class m171117_033420_alter_social_connect extends Migration
{
    public function Up()
    {
        $this->addColumn('social_connect', 'isAutoLogin', $this->boolean()->defaultValue(1)->comment('是否自动登录'));
    }

    public function Down()
    {
        echo "m171117_033420_alter_social_connect cannot be reverted.\n";

        return false;
    }
}
