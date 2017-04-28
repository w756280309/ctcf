<?php

use yii\db\Migration;

class m170428_072027_crm_test extends Migration
{
    public function up()
    {
        $this->createTable('crm_test', [
           'id' => $this->primaryKey(),
            'a' => $this->string(),
            'b' => $this->string(),
            'c' => $this->string(),
            'd' => $this->string(),
            'e' => $this->string(),
            'f' => $this->string(),
            'g' => $this->string(),
            'h' => $this->string(),
            'i' => $this->string(),
            'j' => $this->string(),
            'k' => $this->string(),
            'content' => $this->text(),
            'summary' => $this->text(),
            'status' => $this->integer()->defaultValue(0),
            'type' => $this->string(),
            'error' => $this->text(),
        ]);
    }

    public function down()
    {
        echo "m170428_072027_crm_test cannot be reverted.\n";

        return false;
    }
}
