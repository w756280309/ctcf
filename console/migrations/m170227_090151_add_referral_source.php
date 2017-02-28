<?php

use yii\db\Migration;

class m170227_090151_add_referral_source extends Migration
{
    public function up()
    {
        $this->insert('referral_source', [
            'key' => 'testo2o',
            'target' => 'https://m.wenjf.com/promotion/o2o0301?utm_source=testo2o',
            'title' => 'test-o2o',
            'isActive' => true,
            'source' => 'testo2o',
            'isO2O' => true,
        ]);
    }

    public function down()
    {
        echo "m170227_090151_add_referral_source cannot be reverted.\n";

        return false;
    }
}
