<?php

use yii\db\Migration;

/**
 * Handles the creation of table `recharge_record`.
 */
class m171024_015738_create_recharge_record_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('recharge_record', [
            'id' => $this->primaryKey(10)->unsigned(),
            'sn' => $this->string(30)->unique()->defaultValue(Null)->comment('流水号'),
            'pay_type' => $this->smallInteger()->notNull()->comment('1快捷充值,2网银充值'),
            'pay_id' => $this->integer(1)->unsigned()->notNull()->comment('支付公司id'),
            'account_id' => $this->integer(10)->unsigned()->notNull()->comment('对应资金账户id'),
            'uid' => $this->integer(10)->unsigned()->defaultValue(Null),
            'fund' => $this->decimal(14, 2)->unsigned()->defaultValue(0.00)->comment('充值金额'),
            'epayUserId' => $this->string(60)->notNull()->comment('托管平台用户号'),
            'clientIp' => $this->integer(10)->notNull()->comment('ip地址'),
            'pay_bank_id' => $this->string(30)->notNull()->comment('取现银行代号【不同支付公司银行id可能不等。保存时按照统一的保存bank_id】'),
            'bank_id' => $this->string(30)->notNull()->comment('本平台银行id'),
            'bankNotificationTime' => $this->dateTime()->defaultValue(Null)->comment('支付平台收到银行通知时间，格式：YYYYMMDDhhmmss'),
            'settlement' => $this->smallInteger()->defaultValue(0)->comment('结算状态0未结算 10=已经受理 30=正在结算 40=已经执行(已发送转账指令) 50=转账退回'),
            'remark' => $this->string(100)->defaultValue(Null)->comment('remark'),
            'created_at' => $this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at' => $this->integer(10)->unsigned()->defaultValue(Null),
            'status' => $this->smallInteger()->unsigned()->defaultValue(0)->comment('状态 0-充值未处理   1-充值成功 2充值失败 '),
            'lastCronCheckTime' => $this->integer(10)->defaultValue(Null),
        ]);
        $this->createIndex(
            'account_id',
            'recharge_record',
            'account_id'
        );
        $this->createIndex(
            'uid',
            'recharge_record',
            'uid'
        );
        $this->createIndex(
            'status',
            'recharge_record',
            'status'
        );
        $this->createIndex(
            'status_2',
            'recharge_record',
            'status'
        );

        $this->createIndex(
            'uid_2',
            'recharge_record',
            'uid'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('recharge_record');
    }
}
