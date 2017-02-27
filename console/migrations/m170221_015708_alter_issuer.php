<?php

use yii\db\Migration;

class m170221_015708_alter_issuer extends Migration
{
    public function up()
    {
        $this->addColumn('issuer', 'allowShowOnPc', $this->boolean()->defaultValue(false)->notNull());
        $this->addColumn('issuer', 'pcTitle', $this->string());
        $this->addColumn('issuer', 'pcDescription', $this->string());
        $this->addColumn('issuer', 'pcLink', $this->string());
    }

    public function down()
    {
        echo "m170221_015708_alter_issuer cannot be reverted.\n";

        return false;
    }
}
