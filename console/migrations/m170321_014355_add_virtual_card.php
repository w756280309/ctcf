<?php

use common\models\affiliation\Affiliator;
use common\models\code\GoodsType;
use common\models\user\User;
use common\utils\SecurityUtils;
use yii\db\Migration;

class m170321_014355_add_virtual_card extends Migration
{
    public function up()
    {
        $mobiles = [
            '13738316120', //陈佳佳
            '13676752821', //叶优翔
            '18530089786', //刘凤君
            '18519099691', //孙守正
            '13488235332', //于文倩
            '13521883086', //陈方方
            '18518154492', //左钰玮
        ];
        $affiliator = Affiliator::find()->where(['name' => 'O2O测试商家'])->one();
        $goods = GoodsType::find()->where(['sn' => '20170320testO2O', 'type' => 3])->one();
        if (null !== $affiliator && null !== $goods) {
            foreach ($mobiles as $key => $mobile) {
                if (null !== ($user = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile)]))) {
                    $this->insert('virtual_card', [
                        'serial' => 'TEST' . date('YmdHis') . rand(100000, 999999),
                        'user_id' => $user->id,
                        'isPull' => true,
                        'pullTime' => date('Y-m-d H:i:s'),
                        'createTime' => date('Y-m-d H:i:s'),
                        'goodsType_id' => $goods->id,
                        'expiredTime' => $key < 5 ? date('Y-m-d H:i:s', strtotime('+10 days')) : date('Y-m-d H:i:s'),
                        'affiliator_id' => $affiliator->id,
                    ]);
                }
            }
        }
    }

    public function down()
    {
        echo "m170321_014355_add_virtual_card cannot be reverted.\n";

        return false;
    }
}
