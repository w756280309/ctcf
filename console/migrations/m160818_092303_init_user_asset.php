<?php

use yii\db\Migration;

class m160818_092303_init_user_asset extends Migration
{
    public function up()
    {
        $data = \common\models\order\OnlineOrder::find()
            ->select(['uid', 'id', 'online_pid', 'order_money', 'order_time', 'created_at'])
            ->where(['status' => 1])
            ->asArray()
            ->all();
        $this->batchInsert('user_asset', ['user_id', 'order_id', 'loan_id', 'amount', 'created_at', 'updated_at'], $data);
    }

    public function down()
    {
        echo "m160818_092303_init_user_asset cannot be reverted.\n";
        $this->truncateTable('user_asset');
        return false;
    }
}
