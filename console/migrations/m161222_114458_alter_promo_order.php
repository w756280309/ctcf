<?php

use yii\db\Migration;

class m161222_114458_alter_promo_order extends Migration
{
    public function up()
    {
        $this->renameColumn('point_order', 'createdAt', 'created_at');
        $this->renameColumn('point_order', 'updatedAt', 'updated_at');
    }

    public function down()
    {
        echo "m161222_114458_alter_promo_order cannot be reverted.\n";

        return false;
    }
}
