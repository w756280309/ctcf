<?php

use yii\db\Migration;

/**
 * Handles the creation for table `promo_mobile`.
 */
class m170216_012815_create_promo_mobile_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('promo_mobile', [
            'id' => $this->primaryKey(),
            'promo_id' => $this->integer()->notNull(),
            'mobile' => $this->string()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('promo_mobile');
    }
}
