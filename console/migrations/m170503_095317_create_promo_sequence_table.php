<?php

use yii\db\Migration;

/**
 * Handles the creation for table `promo_sequence`.
 */
class m170503_095317_create_promo_sequence_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('promo_sequence', [
            'id' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('promo_sequence');
    }
}
