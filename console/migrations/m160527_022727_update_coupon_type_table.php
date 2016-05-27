<?php

use yii\db\Migration;

class m160527_022727_update_coupon_type_table extends Migration
{
    public function up()
    {
        $this->alterColumn('coupon_type', 'sn', $this->string(20)->unique());
    }

    public function down()
    {
        $this->alterColumn('coupon_type', 'sn', $this->string(20)->notNull()->unique());
    }
}
