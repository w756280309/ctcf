<?php

use yii\db\Migration;

class m170531_073836_callout_responder extends Migration
{
    public function up()
    {
        $this->createTable('callout_responder', [
            'id' => $this->primaryKey(),
            'openid' => $this->string(64)->comment('用户开放身份标识'),
            'callout_id' => $this->integer()->notNull()->comment('召集ID'),
            'ip' => $this->string(15)->comment('响应人IP'),
            'createTime' => $this->dateTime()->comment('创建时间'),
        ]);
    }

    public function down()
    {
        echo "m170531_073836_callout_responder cannot be reverted.\n";

        return false;
    }
}
