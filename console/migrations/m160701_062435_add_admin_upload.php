<?php

use yii\db\Migration;

class m160701_062435_add_admin_upload extends Migration
{
    public function up()
    {
        $this->createTable("admin_upload", [
            'id' => $this->primaryKey(),
            'title' => $this->string(30)->notNull(),
            'link' => $this->string(100)->notNull(),
            'allowHtml' => $this->smallInteger(1)->defaultValue(0),
            'isDeleted' => $this->smallInteger(1)->defaultValue(0),
            'created_at' => $this->integer(11)->notNull(),
            'updated_at' => $this->integer(11)->notNull(),
        ]);
    }

    public function down()
    {
        echo "m160701_062435_add_admin_upload cannot be reverted.\n";

        return false;
    }
}
