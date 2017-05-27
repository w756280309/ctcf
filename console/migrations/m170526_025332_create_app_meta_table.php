<?php

use yii\db\Migration;

/**
 * Handles the creation of table `app_meta`.
 */
class m170526_025332_create_app_meta_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('app_meta', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'key' => $this->string(64)->notNull()->unique(),
            'value' => $this->string()->notNull(),
        ]);
        $this->insert('app_meta', [
            'name' => '自愿捐赠金额',
            'key' => 'donation_total',
            'value' => 0,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {

    }
}
