<?php

use yii\db\Migration;

class m180409_053141_alter_login_log extends Migration
{
    public function up()
    {
        $this->addColumn('login_log', 'status', $this->boolean()->null()->defaultValue(0)->comment('状态：0-失败，1-成功'));
    }

    public function down()
    {
        echo "m180409_053141_alter_login_log cannot be reverted.\n";

        return false;
    }
}
