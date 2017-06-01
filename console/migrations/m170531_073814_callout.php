<?php

use yii\db\Migration;

class m170531_073814_callout extends Migration
{
    public function up()
    {
        $this->createTable('callout', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull()->comment('召集人ID'),
            'endTime' => $this->dateTime()->comment('召集截止时间'),
            'responderCount' => $this->integer()->defaultValue(0)->comment('响应人数'),
            'promo_id' => $this->integer()->null()->comment('参与活动ID'),
            'createTime' => $this->dateTime()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        echo "m170531_073814_callout cannot be reverted.\n";

        return false;
    }
}
