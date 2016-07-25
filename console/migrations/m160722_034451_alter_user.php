<?php

use yii\db\Migration;

class m160722_034451_alter_user extends Migration
{
    public function up()
    {
        //注册来源 0表示未知，1表示wap，2表示wx，3表示app，4表示pc
        $this->renameColumn('user', 'login_from', 'regFrom');
        $this->alterColumn('user', 'regFrom', $this->integer(1)->defaultValue(0));
    }

    public function down()
    {
        echo "m160722_034451_alter_user cannot be reverted.\n";

        return false;
    }
}
