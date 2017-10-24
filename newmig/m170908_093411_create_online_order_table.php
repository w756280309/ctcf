<?php

use yii\db\Migration;

/**
 * Handles the creation of table `online_order`.
 */
class m170908_093411_create_online_order_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('online_order', [
            'id' => $this->primaryKey(10)->comment('投标ID'),
            'sn' => $this->string(30)->notNull()->unique()->comment("订单序号"),
            'online_pid'=>$this->integer(10)->unsigned()->defaultValue(NULL)->comment('项目标的ID'),
            'refund_method'=>$this->boolean()->defaultValue(1)->comment("还款方式：1.按天到期本息 2.按月付息还本"),
            'yield_rate'=>$this->decimal(14,6)->notNull()->comment("年利率"),
            'expires'=>$this->smallInteger(6)->unsigned()->defaultValue(NULL)->comment("借款期限 (以天为单位) 如 15  表示15天"),
            'order_money'=>$this->decimal(14,2)->defaultValue(NULL)->comment("投标金额"),
            'order_time'=>$this->integer(10)->notNull()->comment("成功时间(支付之后)"),
            'uid'=>$this->integer(10)->defaultValue(NULL)->comment("投资者uid"),
            'username'=>$this->string(50)->defaultValue(Null)->comment("投资者用户名"),
            'status'=>$this->smallInteger()->notNull()->comment("0--投标失败---1-投标成功 2.撤标 3，无效"),
            'created_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'campaign_source'=>$this->string(50)->defaultValue(Null)->comment('百度统计来源标志'),
            'couponAmount'=>$this->decimal(6,2)->notNull(),
            'paymentAmount'=>$this->decimal(14,2)->notNull(),
            'investFrom'=>$this->integer(1)->defaultValue(0),
        ]);
        $this->createIndex(
            'online_pid',
            'online_order',
            'online_pid'
        );
        $this->createIndex(
            'uid',
            'online_order',
            'uid'
        );
        $this->createIndex(
            'status',
            'online_order',
            'status'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('online_order');
    }
}
