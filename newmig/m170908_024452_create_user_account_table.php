<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_account`.
 */
class m170908_024452_create_user_account_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_account', [
            'id' => $this->primaryKey()->unsigned(),
            'type' => $this->boolean()->defaultValue(0)->notNull()->comment('类型 1,投资账户 2，融资账户'),
            'uid' => $this->integer(10)->unsigned()->notNull(),
            'account_balance' => $this->decimal(14,2)->defaultValue(0.00)->comment('账户余额'),
            'available_balance' => $this->decimal(14,2)->defaultValue(0.00)->notNull()->comment('可用余额'),
            'freeze_balance' => $this->decimal(14,2)->defaultValue(0.00)->comment('冻结余额'),
            'profit_balance' => $this->decimal(14,2)->defaultValue(0.00)->comment('收益金额'),
            'investment_balance' => $this->decimal(14,2)->defaultValue(0.00)->unsigned()->comment('理财金额'),
            'drawable_balance'=>$this->decimal(14,2),
            'in_sum' => $this->decimal(14,2)->defaultValue(0.00)->unsigned()->comment('账户入金总额'),
            'out_sum' => $this->decimal(14,2)->defaultValue(0.00)->unsigned()->comment('账户出金总额'),
            'created_at' => $this->integer(10)->unsigned()->comment('创建时间'),
            'updated_at' => $this->integer(10)->unsigned(),
        ]);
        $this->createIndex(
            'idx_uid',
            'user_account',
            'uid'
        );
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_account');
    }
}
