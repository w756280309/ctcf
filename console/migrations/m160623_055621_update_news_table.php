<?php

use yii\db\Migration;

class m160623_055621_update_news_table extends Migration
{
    public function up()
    {
        $this->dropColumn("news", "attach_file");
        $this->addColumn("news", "pc_thumb", $this->string(255)->notNull());
    }

    public function down()
    {
        echo "m160623_055621_update_news_table cannot be reverted.\n";

        return false;
    }
}
