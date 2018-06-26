<?php

use yii\db\Migration;

class m180528_073326_create_table_asset extends Migration
{
    public function up()
    {
        $this->createTable('asset', [
            'id' => $this->primaryKey(),
            'source' => $this->string()->notNull()->comment('渠道信息'),
            'createTime' => $this->dateTime()->notNull()->comment('创建时间'),
            'borrowerName' => $this->string()->notNull()->comment('融资方'),
            'sn' => $this->string()->unique()->notNull()->comment('小微系统资产编号'),
            'amount' => $this->decimal(14, 2)->notNull()->comment('资产金额'),
            'repaymentType' => $this->integer()->notNull()->comment('还款方式'),
            'rate' => $this->decimal(10, 4)->notNull()->comment('资产打包利率'),
            'expires' => $this->integer()->notNull()->comment('产品期限'),
            'expiresType' => $this->smallInteger()->notNull()->comment('期限单位,1-天 2-月'),
            'borrowerIdCardNumber' => $this->string()->notNull()->comment('融资方身份证'),
            'borrowerType' => $this->integer()->notNull()->comment('融资方身份,0-个人,1-企业'),
            'extendInfo' => $this->text()->notNull()->comment('扩展信息'),
            'status' => $this->smallInteger()->notNull()->comment('状态'),
            'issue' => $this->boolean()->notNull()->comment('是否可发标 1-不可发 0-可发')
        ]);
        $this->createIndex('borrowerName', 'asset', ['borrowerName']);
    }

    public function down()
    {
        echo "m180528_073326_create_table_asset cannot be reverted.\n";

        return false;
    }
}
