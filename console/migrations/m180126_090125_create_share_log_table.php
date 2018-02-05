<?php

use yii\db\Migration;

/**
 * Handles the creation of table `share_log`.
 */
class m180126_090125_create_share_log_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('share_log', [
            'id' => $this->primaryKey()->unsigned(),
            'uid' => $this->integer()->notNull()->comment('用户id'),
            'scene' => $this->string(10)->notNull()->comment('分享场景'),
            'shareUrl' => $this->string(255)->notNull()->comment('分享的url'),
            'ipAddress' => $this->string(50)->comment('ip地址'),
            'createdAt' => $this->date()->notNull()->comment('分享日期'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('share_log');
    }
}
