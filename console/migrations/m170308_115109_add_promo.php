<?php

use yii\db\Migration;

class m170308_115109_add_promo extends Migration
{
    public function up()
    {
        $config = [
            'image' => 'https://static.wenjf.com/upload/link/link1488952318509571.png',
            'rules' => [
                '此活动限温都金服新用户参加；',
                '新用户首次认购1000元及以上的理财产品即可获得70元美食券；',
                '美食券将以短信形式发送到您的手机，请注意查收并妥善保存；',
                '结账时出示短信即可享受减免70元优惠；',
                '同一订单可支持多张美食券叠加使用，不同订单不可拼单；',
                '本美食券不与其他优惠活动叠加；',
                '本美食券不可兑换现金、不设找零，不支持外卖；',
                '本美食券有效期30天，周末、节假日通用。',
            ],
            'trackCode' => ['yikecoffee'],
            'goodsSn' => 'yikecoffee',
        ];
        $this->insert('promo', [
            'title' => '你聚餐我买单',
            'key' => 'coffee',
            'promoClass' => 'common\models\promo\PromoCorp',
            'isOnline' => true,
            'startTime' => '2017-03-10 00:00:00',
            'endTime' => '2017-04-09 23:59:59',
            'config' => json_encode($config),
        ]);
    }

    public function down()
    {
        echo "m170308_115109_add_promo cannot be reverted.\n";

        return false;
    }
}
