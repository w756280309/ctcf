<?php

use yii\db\Migration;

/**
 * Handles the creation of table `identity_verification`.
 */
class m180816_020123_create_verification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('verification', [
            'id' => $this->primaryKey()->unsigned(),
            'userId' => $this->integer()->unsigned()->null()->comment('用户id'),
            'name' => $this->string(50)->notNull()->comment('加密后的姓名'),
            'idCardNum' => $this->string()->notNull()->comment('加密后的身份证号'),
            'status' => $this->string(30)->notNull()->defaultValue('verifying')->comment('开户状态'),
            'identityId' => $this->integer()->null()->comment('identity表id'),
            'created_at' => $this->integer()->comment('创建时间'),
            'updated_at' => $this->integer()->comment('更新时间'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('verification');
    }
}
