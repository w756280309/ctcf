<?php

use yii\db\Migration;

class m161226_064250_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'annualInvestment', $this->decimal(14, 2)->defaultValue(0)->notNull());
        $this->dropColumn('user', 'coins');
        $this->dropColumn('user', 'level');
    }

    public function down()
    {
        echo "m161226_064250_alter_user_table cannot be reverted.\n";

        return false;
    }
}
