<?php

use yii\db\Migration;

class m170621_024103_award extends Migration
{
    public function up()
    {
        $this->createTable('award', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'createTime' => $this->dateTime(),
            'promo_id' => $this->integer(),
            'ticket_id' => $this->integer(),
            'amount' => $this->decimal(14, 2)->comment('奖励面值'),
            'ref_type' => $this->string(),
            'ref_id' => $this->integer(),
        ]);
    }

    public function down()
    {
        echo "m170621_024103_award cannot be reverted.\n";

        return false;
    }
}
