<?php

use yii\db\Migration;
use common\models\user\User;

class m160505_023846_alter_table_user_add_column extends Migration
{
    public function up()
    {
        $this->addColumn(User::tableName(), 'is_soft_deleted', $this->integer(1)->defaultValue(0));
    }

    public function down()
    {
        echo "m160505_023846_alter_table_user_add_column cannot be reverted.\n";
        return false;
    }

}
