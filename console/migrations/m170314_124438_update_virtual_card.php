<?php

use common\models\affiliation\Affiliator;
use common\models\code\GoodsType;
use yii\db\Migration;

class m170314_124438_update_virtual_card extends Migration
{
    public function up()
    {
        $affiliator = Affiliator::findOne(['name' => '意克咖啡']);
        $goodsType = GoodsType::findOne(['sn' => 'yikecoffee']);
        $this->update('virtual_card', [
            'affiliator_id' => $affiliator->id,
        ], [
            'goodsType_id' => $goodsType->id,
        ]);
    }

    public function down()
    {
        echo "m170314_124438_update_virtual_card cannot be reverted.\n";

        return false;
    }
}
