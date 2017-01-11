<?php

use yii\db\Migration;

class m170111_032119_insert_offline_stats_table extends Migration
{
    public function up()
    {
        $this->insert('offline_stats', [
            'tradedAmount' => 397888000,
            'refundedPrincipal' => 232260000,
            'refundedInterest' => 18396900,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        echo "m170111_032119_insert_offline_stats_table cannot be reverted.\n";

        return false;
    }
}
