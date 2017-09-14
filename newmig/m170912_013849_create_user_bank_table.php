<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user_bank`.
 */
class m170912_013849_create_user_bank_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user_bank', [
            'id' => $this->primaryKey(10)->unsigned(),
            'binding_sn'=>$this->string(32)->unique()->defaultValue(Null)->comment('绑卡流水号'),
            'uid'=>$this->integer(10)->unique()->unsigned()->defaultValue(Null)->comment('用户Id'),
            'epayUserId'=>$this->string(60)->notNull()->comment('托管平台用户号'),
            'bank_id'=>$this->string(255)->defaultValue(Null)->comment('银行id'),
            'bank_name'=>$this->string(255)->defaultValue(Null)->comment('银行名称'),
            'sub_bank_name'=>$this->string(255)->defaultValue(Null)->comment('开户支行名称'),
            'province'=>$this->string(30)->defaultValue(Null)->comment('省'),
            'city'=>$this->string(30)->defaultValue(Null)->comment('城市'),
            'account'=>$this->string(30)->defaultValue(Null)->comment('持卡人姓名'),
            'card_number'=>$this->string(50)->defaultValue(Null)->comment('银行卡号'),
            'account_type'=>$this->smallInteger()->unsigned()->defaultValue(11)->comment('11=个人账户 12=企业账户'),
            'mobile'=>$this->string(11)->defaultValue(Null)->comment('手机号码'),
            'created_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'extCardId'=>$this->string(32)->unsigned()->defaultValue(Null)->comment('卡ID'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_bank');
    }
}
