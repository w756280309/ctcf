<?php

use yii\db\Migration;

/**
 * Class m180822_081041_alter_identity_table
 */
class m180822_081041_alter_identity_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('identity', 'encryptedName', $this->string()->notNull()->after('id')->comment('用户姓名'));
        $this->addColumn('identity', 'userId', $this->integer()->null()->after('id')->comment('用户id'));
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropColumn('identity', 'encryptedName');
        $this->dropColumn('identity', 'userId');
    }
}
