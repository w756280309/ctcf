<?php

use yii\db\Migration;

class m170821_013200_alter_online_product extends Migration
{
    public function Up()
    {
        $this->addColumn('online_product', 'balance_limit', $this->decimal(14,2)->defaultValue('0.00'));
    }

    public function Down()
    {
        echo "m170821_013200_alter_online_product cannot be reverted.\n";

        return false;
    }
}
