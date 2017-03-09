<?php

use yii\db\Migration;

class m170308_115128_alter_referral_source extends Migration
{
    public function up()
    {
        $this->update('referral_source', [
            'target' => 'https://m.wenjf.com/promotion/coffee?utm_source=yikecoffee',
            'title' => '你聚餐我买单',
            'description' => '意克咖啡',
            'source' => 'yikecoffee',
        ], [
            'key' => 'restaurant',
        ]);
    }

    public function down()
    {
        echo "m170308_115128_alter_referral_source cannot be reverted.\n";

        return false;
    }
}
