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
            'user_id' => $this->integer()->comment('用户ID'),
            'encryptedName' => $this->string()->comment('加密后的姓名'),
            'encryptedIdCard' => $this->string()->comment('加密后的身份证号'),
            'status' => $this->string(10)->comment('状态'),
            'ip' => $this->string()->comment('实名ip'),
            'createTime' => $this->datetime()->comment('创建时间'),
            'updateTime' => $this->datetime()->comment('更新时间'),
            'message' => $this->string()->comment('错误信息'),
            'sn' => $this->string(30),
            'code' => $this->string(30)->comment('联动返回状态码')
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
