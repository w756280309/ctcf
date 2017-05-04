<?php

use yii\db\Migration;

class m170503_101556_insert_promo_sequence_table extends Migration
{
    public function up()
    {
        $this->insert('promo_sequence', [
            'id' => 0,
        ]);
    }

    public function down()
    {
        echo "m170503_101556_insert_promo_sequence_table cannot be reverted.\n";

        return false;
    }
}
