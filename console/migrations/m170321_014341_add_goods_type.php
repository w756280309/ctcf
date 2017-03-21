<?php

use common\models\affiliation\Affiliator;
use yii\db\Migration;

class m170321_014341_add_goods_type extends Migration
{
    public function up()
    {
        $affiliator = Affiliator::find()->where(['name' => 'O2O测试商家'])->one();
        if (null !== $affiliator) {
            $this->insert('goods_type', [
                'sn' => '20170320testO2O',
                'name' => 'testO2O商品',
                'type' => 3,
                'createdAt' => date('Y-m-d H:i:s'),
                'effectDays' => 10,
                'affiliator_id' => $affiliator->id,
            ]);
        }
    }

    public function down()
    {
        echo "m170321_014341_add_goods_type cannot be reverted.\n";

        return false;
    }
}
