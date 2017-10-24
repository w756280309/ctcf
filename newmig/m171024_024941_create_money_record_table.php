<?php

use yii\db\Migration;

/**
 * Handles the creation of table `money_record`.
 */
class m171024_024941_create_money_record_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('money_record', [
            'id' => $this->primaryKey(10)->unsigned(),
            'sn' => $this->string(30)->defaultValue(Null)->comment('流水号'),
            'type' =>$this->integer(1)->notNull()->defaultValue(0)->comment('类型。'),
            'osn' =>$this->string(30)->defaultValue(Null)->comment('对应的流水号'),
            'account_id'=> $this->integer(10)->unsigned()->notNull()->comment('对应资金账户id'),
            'uid' =>$this->integer(10)->unsigned()->defaultValue(Null),
            'balance'=>$this->decimal(14,2)->defaultValue('0.00')->comment('每次流水记录当时余额'),
            'in_money'=>$this->decimal(14,2)->unsigned()->defaultValue('0.00')->comment('入账金额'),
            'out_money'=>$this->decimal(14,2)->unsigned()->defaultValue('0.00')->comment('出账金额'),
            'remark'=>$this->string(500)->defaultValue(Null)->comment('备注'),
            'created_at' =>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at' =>$this->integer(10)->unsigned()->defaultValue(Null),
        ]);
        $this->createIndex(
            'idx_uid',
            'money_record',
            'uid'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('money_record');
    }
}
