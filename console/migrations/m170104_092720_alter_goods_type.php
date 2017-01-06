<?php

use yii\db\Migration;

class m170104_092720_alter_goods_type extends Migration
{
    public function up()
    {
        $this->addColumn('goods_type', 'type', $this->smallInteger());
        $this->addColumn('goods_type', 'createdAt', $this->dateTime());

        $couponSn = [
            '0022:20000-20',
            '0022:50000-50',
            '0022:100000-120',
            '0022:200000-180',
        ];
        $giftSn = [
            'oil_900',
            'recharge_100',
            'woema_100',
            'woema_500',
            'guihuagao_50',
            'oil_4000',
            'baijiebu_10',
            'baowenping_1500',
            'yagao_100',
            'xiaomi_usb',
            'xiaomi',
            'chongdianbao',
            'code_test',
        ];

        $this->update('goods_type', ['type' => 1, 'createdAt' => '2017-01-01 00:00:00'], ['sn' => $couponSn]);
        $this->update('goods_type', ['type' => 2, 'createdAt' => '2017-01-01 00:00:00'], ['sn' => $giftSn]);
    }

    public function down()
    {
        echo "m170104_092720_alter_goods_type cannot be reverted.\n";

        return false;
    }
}
