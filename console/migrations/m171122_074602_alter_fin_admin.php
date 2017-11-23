<?php

use yii\db\Migration;

class m171122_074602_alter_fin_admin extends Migration
{
    public function init()
    {
        $this->db = 'db_fin';
        parent::init();
    }
    public function up()
    {
        $this->addColumn('admin', 'isDel', $this->boolean()->defaultValue(0)->comment('是否删除'));
    }

    public function down()
    {
        echo "m171122_074602_alter_fin_admin cannot be reverted.\n";

        return false;
    }
}
