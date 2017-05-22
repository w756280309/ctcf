<?php

use yii\db\Migration;

class m170521_145009_alter_user extends Migration
{
    public function up()
    {
        $this->dropColumn('user', 'org_url');
        $this->dropColumn('user', 'mobile_status');
        $this->dropColumn('user', 'email_status');
        $this->dropColumn('user', 'draw_status');
        $this->dropColumn('user', 'examin_status');
        $this->dropColumn('user', 'finance_status');
    }

    public function down()
    {
        echo "m170521_145009_alter_user cannot be reverted.\n";

        return false;
    }
}
