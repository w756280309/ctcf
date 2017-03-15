<?php

use common\models\affiliation\Affiliator;
use yii\db\Migration;

class m170314_031928_update_goods_type extends Migration
{
    public function up()
    {
        $Affiliator = Affiliator::findOne(['name' => '意克咖啡']);
        if (null !== $Affiliator) {
            $this->update('goods_type', [
                'affiliator_id' => $Affiliator->id,
                'effectDays' => 30,
            ], [
                'sn' => 'yikecoffee',
            ]);
        }
    }

    public function down()
    {
        echo "m170314_031928_update_goods_type cannot be reverted.\n";

        return false;
    }
}
