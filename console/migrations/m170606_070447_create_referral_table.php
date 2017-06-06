<?php

use yii\db\Migration;

/**
 * Handles the creation of table `referral`.
 */
class m170606_070447_create_referral_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('referral', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'code' => $this->string()->notNull(),
            'created_at' => $this->integer(10),
            'updated_at' => $this->integer(10),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('referral');
    }
}
