<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_old`.
 */
class m180212_020626_create_user_old_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_old', [
            'id' => $this->primaryKey(),
            'userId'=>$this->integer(10)->unique()->unsigned()->defaultValue(null),
            'real_name' => $this->string(50)->comment('真实姓名'),
            'mobile' => $this->string(),
            'idCard' => $this->string(),
            'bankName' => $this->string('50'),
            'cardNumber' => $this->string('50'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_old');
    }
}
