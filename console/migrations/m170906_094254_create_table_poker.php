<?php

use yii\db\Migration;

class m170906_094254_create_table_poker extends Migration
{
    public function Up()
    {
        /*
         * 扑克牌抽奖活动开奖表
         */
        $this->createTable('poker', [
            'id' => $this->primaryKey(),
            'term' =>$this->string(10)->unique()->notNull(),
            'spade' => $this->integer()->notNull(),  //黑桃
            'heart' => $this->integer()->notNull(),   //红桃
            'club' => $this->integer()->notNull(),   //梅花
            'diamond' => $this->integer()->notNull(),   //方块
        ]);
        $this->createIndex('unique_term', 'poker', 'term', true);

    }

    public function Down()
    {
        echo "m170906_094254_create_table_poker cannot be reverted.\n";

        return false;
    }
}
