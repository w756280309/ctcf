<?php

use yii\db\Migration;

/**
 * Handles the creation of table `appliament`.
 */
class m171026_050753_create_appliament_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('appliament', [
            'id' => $this->primaryKey()->unsigned(),
            'userID' => $this->integer()->unsigned()->notNull()->comment('用户ID'),
            'appointmentTime' => $this->integer()->unsigned()->notNull()->comment('预约时间'),
            'appointmentAward' => $this->decimal(14,2)->unsigned()->notNull()->comment('预约金额'),
            'appointmentObjectId' => $this->smallInteger()->unsigned()->notNull()->comment('预约类型'),
            'appointmentAwardType' => $this->smallInteger()->unsigned()->notNull()->comment('获奖类型，1：喜卡，2：加息券'),
        ]);

        $this->createIndex(
            'userId',
            'appliament',
            'userId'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('appliament');
    }
}
