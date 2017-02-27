<?php

use yii\db\Migration;

/**
 * Handles the creation for table `sms_config`.
 */
class m170223_022107_create_sms_config_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('sms_config', [
            'id' => $this->primaryKey(),
            'template_id' => $this->string()->notNull(),
            'config' => $this->text()->notNull(),   //存的是json格式的字符串
            'createTime' => $this->dateTime()->notNull(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('sms_config');
    }
}
