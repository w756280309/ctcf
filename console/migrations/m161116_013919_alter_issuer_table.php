<?php

use yii\db\Migration;

class m161116_013919_alter_issuer_table extends Migration
{
    public function up()
    {
        $this->addColumn('issuer', 'mediaTitle', $this->string());
        $this->addColumn('issuer', 'mediaUri', $this->string());
    }

    public function down()
    {
        echo "m161116_013919_alter_issuer_table cannot be reverted.\n";

        return false;
    }
}
