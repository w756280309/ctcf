<?php

use yii\db\Migration;

class m171204_102514_alter_offlineOrder extends Migration
{
    public function up()
    {
        $this->addColumn('offline_order', 'idcardPic', $this->string()->notNull()->defaultValue(null)->comment('身份证正面'));
        $this->addColumn('offline_order', 'posPic', $this->string()->notNull()->defaultValue(null)->comment('POS单'));
        $this->addColumn('offline_order', 'bankcardPic', $this->string()->notNull()->defaultValue(null)->comment('银行卡'));
    }

    public function down()
    {
        echo "m171204_102514_alter_offlineOrder cannot be reverted.\n";

        return false;
    }
}
