<?php

use yii\db\Migration;

/**
 * Handles the creation of table `open_account`.
 */
class m170911_033328_create_open_account_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('open_account', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'encryptedName' => $this->string(),
            'encryptedIdCard' => $this->string(),
            'status' => $this->string(10),
            'ip' => $this->string(255),
            'createTime' => $this->datetime(),
            'updateTime' => $this->datetime(),
            'message' => $this->string(255),
            'sn' => $this->string(30),
            'code' => $this->string(30)
        ]);
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('open_account');
    }
}
