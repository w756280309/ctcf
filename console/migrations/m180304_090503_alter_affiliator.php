<?php

use yii\db\Migration;

class m180304_090503_alter_affiliator extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'hideSensitiveinfo', $this->boolean()->notNull()->defaultValue(0)->comment('是否隐藏敏感信息'));
    }

    public function down()
    {
        echo "m180304_090503_alter_affiliator cannot be reverted.\n";

        return false;
    }
}
