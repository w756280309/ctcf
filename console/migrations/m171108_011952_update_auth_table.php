<?php

use yii\db\Migration;
use common\models\adminuser\Auth;

class m171108_011952_update_auth_table extends Migration
{
    public function Up()
    {
            $this->update('auth', [
                'auth_name' => '优惠券管理',
                'auth_description' => '优惠券管理',
            ],[
                'sn' => 'A100900'
            ]);
    }

    public function Down()
    {
        echo "m171108_011952_update_auth_table cannot be reverted.\n";

        return false;
    }

}
