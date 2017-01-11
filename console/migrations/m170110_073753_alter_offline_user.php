<?php

use yii\db\Migration;

class m170110_073753_alter_offline_user extends Migration
{
    public function up()
    {
        $this->addColumn('offline_user', 'points', $this->integer()->defaultValue(0));
        $this->addColumn('offline_user', 'annualInvestment', $this->decimal(14, 2)->notNull()->defaultValue('0.00'));
    }

    public function down()
    {
        echo "m170110_073753_alter_offline_user cannot be reverted.\n";

        return false;
    }
}
