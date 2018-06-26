<?php

use yii\db\Migration;

class m180626_085718_alter_asset extends Migration
{
    public function up()
    {
        $this->addColumn('asset', 'itemInfo', $this->text()->null()->defaultValue(null)->comment('拆分资产信息'));
    }

    public function down()
    {
        echo "m180626_085718_alter_asset cannot be reverted.\n";

        return false;
    }
}
