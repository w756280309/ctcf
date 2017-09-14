<?php

use yii\db\Migration;

/**
 * Handles the creation of table `qpaybinding`.
 */
class m170912_011406_create_qpaybinding_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('qpaybinding', [
            'id' => $this->primaryKey(10)->unsigned(),
            'binding_sn'=>$this->string(32)->unique()->notNull()->comment('绑卡流水号'),
            'uid'=>$this->integer(10)->unsigned()->defaultValue(Null)->comment('用户Id'),
            'epayUserId'=>$this->string(60)->notNull()->comment('托管平台用户号'),
            'bank_id'=>$this->string(255)->defaultValue(Null)->comment('银行id'),
            'bank_name'=>$this->string(255)->defaultValue(Null)->comment('银行名称'),
            'sub_bank_name'=>$this->string(255)->defaultValue(Null)->comment('开户支行名称'),
            'province'=>$this->string(30)->defaultValue(Null)->comment('省'),
            'city'=>$this->string(30)->defaultValue(Null)->comment('城市'),
            'account'=>$this->string(30)->defaultValue(Null)->comment('持卡人姓名'),
            'card_number'=>$this->string(50)->defaultValue(Null)->comment('银行卡号'),
            'account_type'=>$this->smallInteger()->unsigned()->defaultValue(11)->comment('11=个人账户 12=企业账户'),
            'status'=>$this->smallInteger()->unsigned()->defaultValue(0)->comment('状态 0-未绑定 1-已绑定 3-处理中'),
            'created_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'safeMobile'=>$this->string(32)->unsigned()->defaultValue(Null)->comment('预留手机号码'),
            'extCardId'=>$this->string(32)->unsigned()->defaultValue(Null)->comment('卡ID'),
            'extTicket'=>$this->string(32)->unsigned()->defaultValue(Null)->comment('后续推进需要的参数'),
        ]);
        $this->createIndex(
            'binding_sn_2',
            'qpaybinding',
            'binding_sn'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('qpaybinding');
    }
}
