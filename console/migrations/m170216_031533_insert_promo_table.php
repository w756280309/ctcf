<?php

use yii\db\Migration;

class m170216_031533_insert_promo_table extends Migration
{
    public function up()
    {
        $config = [
            'image' => '',
            'rules' => [
                '注册送288元红包；',
                '2017年1月18日起首次投资，送积分1400分，可兑换电影票2张；',
                '2017年1月18日起首次投资5万以上，送积分3500分，可兑换电影票5张；',
                '电影票限量1000张，先到先得，兑完为止，电影票领取地址温州市鹿城区飞霞南路657号保丰大楼四层；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];

        $this->insert('promo', [
            'title' => '首次投资送超市卡',
            'startTime' => date('Y-m-d'),
            'endTime' => '',
            'key' => 'wrm170210',
            'promoClass' => '',
            'isOnline' => true,
            'config' => json_encode($config),
        ]);
    }

    public function down()
    {
        echo "m170216_031533_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
