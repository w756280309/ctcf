<?php

use yii\db\Migration;

class m161124_073646_alter_share_table extends Migration
{
    public function up()
    {
        $this->addColumn('share', 'url', $this->string());
    }

    public function down()
    {
        $this->dropColumn('share', 'url');
    }
}
