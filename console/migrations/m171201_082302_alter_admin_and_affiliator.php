<?php

use yii\db\Migration;

class m171201_082302_alter_admin_and_affiliator extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'isBranch', $this->boolean()->defaultValue(0)->comment('是否是门店(网点)'));
        $this->addColumn('admin', 'affiliator_id', $this->integer()->notNull()->defaultValue(null)->comment('门店id'));
    }

    public function down()
    {
        echo "m171201_082302_alter_admin_and_affiliator cannot be reverted.\n";

        return false;
    }
}
