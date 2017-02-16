<?php

use yii\db\Migration;

class m170213_022851_recommend extends Migration
{
    public function up()
    {
        $this->update('affiliator', ['isRecommend' => true], ['in', 'name', ['温州都市报', '衢州日报', '瑞安日报', '金华电视台', '长兴传媒', '舟山日报', '建德广播电视台']]);
    }

    public function down()
    {
        echo "m170213_022851_recommend cannot be reverted.\n";

        return false;
    }
}
