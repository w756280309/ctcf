<?php

use yii\db\Migration;

class m161125_060548_update_share_table extends Migration
{
    public function up()
    {
        $this->update('share', [
            'url' => 'https://m.wenjf.com/site/h5?wx_share_key=h5'
        ], [
            'shareKey' => 'h5',
        ]);
    }

    public function down()
    {
        echo "m161125_060548_update_share_table cannot be reverted.\n";

        return false;
    }
}
