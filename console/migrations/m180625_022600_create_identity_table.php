<?php

use yii\db\Migration;

/**
 * Handles the creation of table `share_log`.
 */
class m180625_022600_create_identity_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('identity', [
            'id' => $this->primaryKey(),
            'encryptedIdCard' => $this->string()->notNull()->comment('加密的身份证号'),
            'create_time' => $this->integer()->notNull()->comment('创建时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('identity');
    }
}
