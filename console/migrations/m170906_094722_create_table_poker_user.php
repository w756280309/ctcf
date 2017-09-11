<?php

use yii\db\Migration;

class m170906_094722_create_table_poker_user extends Migration
{
    /*
     * 扑克牌抽奖活动用户表
     */
    public function Up()
    {
        $this->createTable('poker_user', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11),
            'term' =>$this->string(10)->notNull()->comment('期数'),
            'spade' => $this->integer()->notNull()->defaultValue(0)->comment('黑桃'),  //黑桃
            'heart' => $this->integer()->notNull()->defaultValue(0)->comment('红桃'),   //红桃
            'club' => $this->integer()->notNull()->defaultValue(0)->comment('梅花'),   //梅花
            'diamond' => $this->integer()->notNull()->defaultValue(0)->comment('方块'),   //方块
            'order_id' => $this->integer()->defaultValue(null)->comment('订单id'),
            'firstVisitTime' => $this->dateTime()->defaultValue(null)->comment('本期首次访问时间'),
            'checkInTime' => $this->dateTime()->defaultValue(null)->comment('本期签到时间'),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
        $this->createIndex('user_unique', 'poker_user', ['user_id', 'term'], true);

    }

    public function Down()
    {
        echo "m170906_094722_create_table_poker_user cannot be reverted.\n";

        return false;
    }
}
