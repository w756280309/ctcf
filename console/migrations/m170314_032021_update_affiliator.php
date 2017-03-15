<?php

use yii\db\Migration;

class m170314_032021_update_affiliator extends Migration
{
    public function up()
    {
        $this->update('affiliator', [
            'isO2O' => true,
        ], [
            'name' => '意克咖啡',
        ]);
    }

    public function down()
    {
        echo "m170314_032021_update_affiliator cannot be reverted.\n";

        return false;
    }
}
