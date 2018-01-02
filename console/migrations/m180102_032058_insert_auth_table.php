<?php

use yii\db\Migration;

class m180102_032058_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'A102302',
            'psn' => 'A102300',
            'level' => '3',
            'auth_name' => '闪屏图自动发布',
            'path' => 'adv/splash/publish',
            'type' => '2',
            'auth_description' => '闪屏图自动发布',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function Down()
    {
        echo "m180102_032058_insert_auth_table cannot be reverted.\n";

        return false;
    }


}
