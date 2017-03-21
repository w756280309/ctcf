<?php

use common\models\affiliation\Affiliator;
use yii\db\Migration;

class m170321_014330_add_affiliator extends Migration
{
    public function up()
    {
        $affiliator = Affiliator::find()->where(['name' => 'O2O测试商家'])->one();
        if (null === $affiliator) {
            $this->insert('affiliator', [
                'name' => 'O2O测试商家',
                'isO2O' => true,
            ]);
        }
    }

    public function down()
    {
        echo "m170321_014330_add_affiliator cannot be reverted.\n";

        return false;
    }
}
