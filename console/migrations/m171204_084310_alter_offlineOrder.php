<?php

use yii\db\Migration;

class m171204_084310_alter_offlineOrder extends Migration
{
    public function up()
    {
        $this->addColumn('offline_order', 'pic', $this->string()->notNull()->defaultValue(null)->comment('凭证'));
    }

    public function down()
    {
        echo "m171204_084310_alter_offlineOrder cannot be reverted.\n";

        return false;
    }
}
