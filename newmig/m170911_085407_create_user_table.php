<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m170911_085407_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'type' => $this->integer()->notNull()->defaultValue(2)->comment('会员类别 1-普通用户 2-机构用户'),
            'username' => $this->char(32)->notNull()->comment('会员账号'),
            'usercode' => $this->char(32)->unique()->notNull()->comment('会员编号'),
//            'mobile' =>$this->string(11)->defaultValue(Null)->comment('联系手机号'),
            'email' => $this->string(50)->comment('Email'),
            'real_name' => $this->string(50)->comment('真实姓名'),
//            'idcard'=> $this->string(50)->comment('身份证号'),
            'tel' => $this->string(50)->defaultValue('')->comment('办公电话'),
            'law_master' => $this->string(150)->comment('法定代表人姓名'),
            'law_master_idcard' => $this->string(90)->comment('法定代表人身份证号'),
            'law_mobile' => $this->char(11)->notNull(),
            'business_licence' => $this->string(150)->comment('营业执照号'),
            'org_name' => $this->string(450)->comment('企业名称'),
            'org_code' => $this->string(90)->comment('组织机构代码证号'),
            'shui_code' => $this->string(150)->comment('税务登记证号'),
            'password_hash' => $this->char(128)->notNull()->comment('用户密码hash'),
            'trade_pwd' => $this->char(128)->defaultValue('')->comment('交易密码'),
            'auth_key' => $this->char(128)->comment('cookie权限认证key'),
            'status' => $this->boolean()->defaultValue(1)->comment('状态 0-锁定 1-正常'),
            'idcard_status' => $this->boolean()->notNull()->defaultValue(0)->comment('状态 -1未通过 0-未验证 1-验证通过'),
            'invest_status' => $this->boolean()->defaultValue(1)->comment('投资状态0不可投，1可投，默认可投'),
            'mianmiStatus' => $this->boolean()->defaultValue(0)->comment('投资免密协议是否签署1签署0未签署'),
            'last_login'=>$this->integer(10)->unsigned()->comment('最后一次登录时间'),
            'passwordLastUpdatedTime' => $this->datetime()->comment('最后修改密码时间'),
            'regFrom' => $this->smallInteger()->defaultValue(0)->comment('注册来源'),
            'updated_at' => $this->integer(10)->unsigned()->notNull()->comment('更新时间'),
            'created_at' => $this->integer(10)->unsigned()->notNull()->comment('注册时间'),
            'campaign_source' => $this->string(50)->comment('百度统计来源标志'),
            'is_soft_deleted' => $this->boolean(),
            'sort' => $this->integer(3)->defaultValue(0),
            'regContext' => $this->string()->notNull(),
            'registerIp' => $this->string(),
            'points' => $this->integer()->defaultValue(0),
            'annualInvestment' => $this->decimal(14,2)->defaultValue(0.00)->notNull(),
            'safeMobile' => $this->string(),
            'safeIdCard' => $this->string(),
            'birthdate' => $this->date(),
            'promoId' => $this->integer(),
            'crmAccount_id' => $this->integer(),
            'regLocation' => $this->string(),
        ]);
    }
    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user');
    }
}
