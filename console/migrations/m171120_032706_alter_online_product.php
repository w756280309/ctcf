<?php

use yii\db\Migration;

class m171120_032706_alter_online_product extends Migration
{
    public function Up()
    {
        $this->addColumn('online_product', 'originalBorrower', $this->string(20)->comment('底层融资方'));
    }

    public function Down()
    {
        echo "m171120_032706_alter_online_product cannot be reverted.\n";

        return false;
    }

}
