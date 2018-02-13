<?php

use yii\db\Migration;

class m180213_050916_insert_reward_table extends Migration
{
    public function up()
    {
        $this->insert('reward', [
            'sn' => '1708_points_5',
            'name' => '5积分',
            'ref_type' => 'POINT',
            'ref_amount' => '5.00',
            'createTime' => '2018-01-01 00:00:00',
        ]);
    }

    public function down()
    {
        echo "m180213_050916_insert_reward_table cannot be reverted.\n";

        return false;
    }
}
