<?php

use yii\db\Migration;

class m170106_074528_add_issuer_table extends Migration
{
    public function up()
    {
        $this->addColumn('issuer', 'big_pic', $this->string());
        $this->addColumn('issuer', 'mid_pic', $this->string());
        $this->addColumn('issuer', 'small_pic', $this->string());
        $this->addColumn('issuer', 'isShow', $this->boolean()->defaultValue(false));
        $this->addColumn('issuer', 'sort', $this->integer());
        $this->addColumn('issuer', 'path', $this->string());
    }

    public function down()
    {
        echo "m170106_074528_add_issuer_table cannot be reverted.\n";

        return false;
    }
}
