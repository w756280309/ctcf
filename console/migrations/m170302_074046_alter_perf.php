<?php

use yii\db\Migration;

class m170302_074046_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'onlineInvestment', $this->decimal(14, 2));
        $this->addColumn('perf', 'offlineInvestment', $this->decimal(14, 2));
    }

    public function down()
    {
        echo "m170302_074046_alter_perf cannot be reverted.\n";

        return false;
    }
}
