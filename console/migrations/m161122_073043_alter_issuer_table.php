<?php

use yii\db\Migration;

class m161122_073043_alter_issuer_table extends Migration
{
    public function up()
    {
        $this->dropColumn('issuer', 'mediaUri');
        $this->addColumn('issuer', 'video_id', $this->integer());
        $this->addColumn('issuer', 'videoCover_id', $this->integer());
    }

    public function down()
    {
        echo "m161122_073043_alter_issuer_table cannot be reverted.\n";

        return false;
    }
}
