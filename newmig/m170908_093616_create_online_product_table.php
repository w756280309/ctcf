<?php

use yii\db\Migration;

/**
 * Handles the creation of table `online_product`.
 */
class m170908_093616_create_online_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('online_product', [
            'id' => $this->primaryKey(10)->unsigned(),
            'epayLoanAccountId' => $this->string(15)->notNull()->comment("标的在托管平台的账户号"),
            'title'=>$this->string(128)->notNull()->comment('标的项目名称'),
            'sn'=>$this->char(32)->unique('sn')->notNull()->comment('标的项目编号'),
            'cid'=>$this->integer(10)->unsigned()->notNull()->comment('分类id'),
            'is_xs'=>$this->boolean()->notNull()->comment('是否新手标1是'),
            'recommendTime'=>$this->integer(10)->notNull()->comment('推荐时间'),
            'borrow_uid'=>$this->integer(10)->notNull()->comment('融资用户ID'),
            'yield_rate'=>$this->decimal(6,4)->notNull()->comment('年利率'),
            'jiaxi'=>$this->decimal(3,1)->defaultValue(Null)->comment('加息利率（%）'),
            'fee'=>$this->decimal(14,6)->notNull()->comment('平台手续费（放款时候收取，最小精度百万分之1）'),
            'expires_show'=>$this->string(50)->notNull()->defaultValue('')->comment('还款期限文字显示'),
            'refund_method'=>$this->integer(1)->unsigned()->defaultValue(1)->comment('还款方式：1.按天到期本息 2.按月付息还本'),
            'expires'=>$this->smallInteger(6)->unsigned()->defaultValue(Null)->comment('借款期限 (以天为单位) 如 15  表示15天'),
            'kuanxianqi'=>$this->smallInteger(4)->notNull()->comment('宽限期'),
            'money'=>$this->decimal(14,2)->notNull()->defaultValue('0.00')->comment('项目融资总额'),
            'funded_money'=>$this->decimal(14,2)->notNull()->comment('实际募集金额'),
            'start_money'=>$this->decimal(14,2)->notNull()->comment('起投金额'),
            'dizeng_money'=>$this->decimal(14,2)->notNull()->comment('递增金额'),
            'finish_date'=>$this->integer(10)->defaultValue(Null)->comment('项目截止日'),
            'start_date'=>$this->integer(10)->notNull()->comment('融资开始日期'),
            'end_date'=>$this->integer(10)->notNull()->comment('融资结束日期'),
            'channel'=>$this->boolean()->defaultValue(0)->comment('分销渠道'),
            'description'=>$this->text()->notNull()->comment('项目介绍'),
            'full_time'=>$this->integer(10)->notNull()->comment('满标时间'),
            'jixi_time'=>$this->integer(10)->defaultValue(Null)->comment('计息开始时间'),
            'fk_examin_time'=>$this->integer(10)->defaultValue(0)->comment('放款审核时间'),
            'account_name'=>$this->string(50)->defaultValue('')->comment('账户名称'),
            'account'=>$this->string(50)->defaultValue('')->comment('账户'),
            'bank'=>$this->string(100)->defaultValue('')->comment('开户行'),
            'del_status'=>$this->integer(10)->defaultValue(0)->comment('状态1-无效 0-有效'),
            'online_status'=>$this->boolean()->defaultValue(0)->comment('上线状态：1上线0未上线'),
            'status'=>$this->smallInteger()->notNull()->defaultValue('1')->comment('标的进展： 1预告期、 2进行中,3满标,4流标,5还款中,6已还清'),
            'yuqi_faxi'=>$this->decimal(14,6)->notNull()->comment('逾期罚息'),
            'order_limit'=>$this->integer(4)->defaultValue(200)->comment('限制投标uid，默认200次'),
            'isPrivate'=>$this->boolean()->defaultValue(0)->comment('是否是定向标，0否1是'),
            'allowedUids'=>$this->string(200)->defaultValue(Null)->comment('定向标用户id。以,分隔'),
            'finish_rate'=>$this->decimal(6,4)->defaultValue(0.0000)->comment('募集完成比例'),
            'is_jixi'=>$this->boolean()->defaultValue(0)->comment('是否已经计息0否1是'),
            'sort'=>$this->boolean()->defaultValue(0)->comment('排序'),
            'contract_type'=>$this->boolean()->defaultValue(0)->comment('0固定1特殊模板'),
            'creator_id'=>$this->integer(10)->unsigned()->notNull()->comment('创建者管理员id'),
            'created_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'updated_at'=>$this->integer(10)->unsigned()->defaultValue(Null),
            'isFlexRate'=>$this->boolean()->defaultValue(0)->comment('是否启用浮动利率'),
            'rateSteps'=>$this->string(500)->defaultValue(Null)->comment('浮动利率'),
            'issuer'=>$this->integer(11)->defaultValue(Null)->comment('发行方'),
            'issuerSn'=>$this->string(30)->defaultValue(Null)->comment('发行方项目编号'),
            'paymentDay'=>$this->integer(5)->defaultValue(Null)->comment('固定还款日'),
            'isTest'=>$this->boolean()->defaultValue(0)->comment('是否测试标'),
            'filingAmount'=>$this->decimal(14,2)->defaultValue(Null)->comment('备案金额'),
            'allowUseCoupon'=>$this->boolean()->notNull()->comment('是否可以使用代金券'),
            'tags'=>$this->string(255)->defaultValue(Null)->comment('标签'),
            'isLicai'=>$this->boolean()->defaultValue(Null)->comment('是否为理财计划标识'),
            'pointsMultiple'=>$this->smallInteger(6)->defaultValue(1)->comment('积分倍数'),
            'allowTransfer'=>$this->boolean()->defaultValue(1)->comment('是否允许转让'),
            'isCustomRepayment'=>$this->boolean()->defaultValue(Null)->comment('是否自定义还款'),
            'isJixiExamined'=>$this->boolean()->defaultValue(1)->comment('计息审核状态'),
            'internalTitle'=>$this->string(30)->defaultValue(Null)->comment('项目副标题'),
            'publishTime'=>$this->dateTime()->defaultValue(Null)->comment('产品上线时间'),
            'balance_limit'=>$this->decimal(14,2)->defaultValue(0.00)->comment('部分用户可见（默认值为100000）'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('online_product');
    }
}
