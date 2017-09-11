<?php

use yii\db\Migration;

/**
 * Handles the creation of table `epayuser`.
 * Has foreign keys to the tables:
 *
 * - `appUserId`
 * - `epayId`
 * - `epayUserId`
 */
class m170908_014514_create_epayuser_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('epayuser', [
            'id' => $this->primaryKey()->unsigned()->comment('ID'),
            'appUserId' => $this->string(60)->notNull()->comment('应用方用户ID（兼容非数字的用户标识）'),
            'epayId' => $this->smallInteger(5)->notNull()->unsigned()->comment('托管方ID'),
            'epayUserId' => $this->string(60)->notNull()->comment('托管用户ID'),
            'accountNo' => $this->string(60)->comment('托管账户号'),
            'regDate' => $this->date()->notNull()->comment('开户日期'),
            'clientIp' => $this->integer(10)->notNull()->unsigned()->comment('IP'),
            'createTime' => $this->datetime()->notNull()->comment('记录时间'),
        ]);
        // creates index for column `appUserId`
        $this->createIndex(
            'appUserId',
            'epayuser',
            'appUserId'
        );
        // creates index for column `epayId`
        $this->createIndex(
            'epayId',
            'epayuser',
            'epayId'
        );
        // creates index for column `epayUserId`
        $this->createIndex(
            'epayId_2',
            'epayuser',
            'epayId,epayUserId',
            '1'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops index for column `appUserId`
        $this->dropIndex(
            'appUserId',
            'epayuser'
        );
        // drops index for column `epayId`
        $this->dropIndex(
            'epayId',
            'epayuser'
        );
        // drops index for column `epayUserId`
        $this->dropIndex(
            'epayId_2',
            'epayuser'
        );
        $this->dropTable('epayuser');
    }
}
