<?php

use yii\db\Migration;

class m170112_081754_add_point_record extends Migration
{
    public function up()
    {
        $this->addColumn('point_record', 'remark', $this->string());
    }

    public function down()
    {
        echo "m170112_081754_add_point_record cannot be reverted.\n";

        return false;
    }
}
