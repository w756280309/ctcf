<?php

use yii\db\Migration;

class m160705_051053_promo_lottery_ticket extends Migration
{
    public function up()
    {
        $this->createTable('promo_lottery_ticket', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'isDrawn' => $this->integer(1)->defaultValue(0),
            'isRewarded' => $this->integer(1)->defaultValue(0),
            'reward_id' => $this->integer(1)->defaultValue(0),
            'ip' => $this->string(30),
            'rewardedAt' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }
}
