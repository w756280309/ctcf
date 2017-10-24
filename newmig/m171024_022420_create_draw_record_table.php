<?php

use yii\db\Migration;

/**
 * Handles the creation of table `draw_record`.
 */
class m171024_022420_create_draw_record_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('draw_record', [
            'id' => $this->primaryKey(10)->unsigned(),
            'pay_id' => $this->integer(1)->unsigned()->notNull()->comment('支付公司id【若线下划款，此字段没有意义】'),
            'account_id' => $this->integer(10)->unsigned()->notNull()->comment('对应资金账户id'),
            'sn' => $this->string(30)->defaultValue(Null)->comment('流水号'),
            'orderSn' =>$this->string('30')->defaultValue(Null),
            'uid' => $this->integer(10)->unsigned()->notNull(),
            'identification_type' => $this->smallInteger()->notNull()->comment('证件类型0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证'),
            'identification_number' => $this->string(32)->notNull()->comment('证件号'),
            'user_bank_id' => $this->integer(11)->notNull()->comment('userbank的id'),
            'sub_bank_name' => $this->string(255)->defaultValue(Null),
            'province' => $this->string(30)->defaultValue(Null),
            'city' => $this->string(30)->defaultValue(Null),
            'money' => $this->decimal(14,2)->unsigned()->notNull()->defaultValue('0.00')->comment('提现金额'),
            'fee' => $this->decimal(4,2)->notNull()->comment('提现手续费'),
            'pay_bank_id' => $this->string(30)->notNull()->comment('取现银行代号【不同支付公司银行id可能不等。保存时按照统一的保存bank_id】'),
            'bank_id' => $this->string(30)->notNull()->comment('本平台银行id'),
            'bank_name' =>$this->string(30)->notNull()->comment('取现银行账户'),
            'bank_account' =>$this->string(30)->notNull()->comment('取现银行账号'),
            'status' =>$this->smallInteger()->unsigned()->defaultValue(0)->comment('状态 0-未处理 1-已审核 21-提现不成功  2-提现成功 11-提现驳回'),
            'created_at' =>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at' =>$this->integer(10)->unsigned()->defaultValue(Null),
            'lastCronCheckTime' => $this->integer(10)->defaultValue(Null),
        ]);
        $this->createIndex(
            'idx_uid',
            'draw_record',
            'uid'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('draw_record');
    }
}
