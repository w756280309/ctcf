<?php

use yii\db\Migration;

class m170217_021607_update_promo_table extends Migration
{
    public function up()
    {
        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1487318400276495.png',
            'rules' => [
                '注册送288元红包；',
                '2017年1月18日起首次投资，送积分1400分，可兑换电影票2张；',
                '2017年1月18日起首次投资5万以上，送积分3500分，可兑换电影票5张；',
                '电影票限量1000张，先到先得，兑完为止，电影票领取地址温州市鹿城区飞霞南路657号保丰大楼四层；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];

        $this->update('promo', [
            'config' => json_encode($config),
        ], [
            'key' => 'promo_movie',
        ]);

        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1487300703304778.png',
            'rules' => [
                '注册送288元红包；',
                '首次投资，送积分1400分，可兑换温都猫充值卡70元；',
                '首次投资5万以上，送积分3500分，可兑换温都猫充值卡175元；',
                '温都猫充值卡限量1000张，先到先得，兑完为止；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];

        $this->update('promo', [
            'config' => json_encode($config),
        ], [
            'key' => 'wendumao',
        ]);

        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1487300680432377.jpg',
            'rules' => [
                '注册送288元红包；',
                '2017年1月26日起首次投资，送积分1400分，可兑换星际嘉年华亲子套票一份；',
                '2017年1月26日起首次投资5万以上，送积分3500分，可以在积分商城兑换其他产品；',
                '星际嘉年华亲子套票领取地址：温州市鹿城区飞霞南路657号保丰大楼四层；',
                '活动时间：1月26日至3月10日；',
                '如发现一人注册多个账户、刷单等恶意行为，温都金服有权取消活动资格。',
            ],
        ];

        $this->update('promo', [
            'config' => json_encode($config),
        ], [
            'key' => 'promo_space',
        ]);
    }

    public function down()
    {
        echo "m170217_021607_update_promo_table cannot be reverted.\n";

        return false;
    }
}
