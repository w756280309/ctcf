<?php

use yii\db\Migration;

class m170323_072156_alter_points_batch extends Migration
{
    public function up()
    {
        $this->addColumn('points_batch', 'user_id', $this->integer());
    }

    public function down()
    {
        echo "m170323_072156_alter_points_batch cannot be reverted.\n";

        return false;
    }
}
