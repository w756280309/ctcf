<?php

use yii\db\Migration;

class m170413_081316_check_in extends Migration
{
    public function up()
    {
        $this->createTable('check_in', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'checkDate' => $this->date()->notNull(),
            'lastCheckDate' => $this->date(),//上次签到时间
            'streak' => $this->integer(),//连续签到次数
            'createTime' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex('unique_user_date', 'check_in', ['user_id', 'checkDate'], true);
    }

    public function down()
    {
        echo "m170413_081316_check_in cannot be reverted.\n";

        return false;
    }
}
