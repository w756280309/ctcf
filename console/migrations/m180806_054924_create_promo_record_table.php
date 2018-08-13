<?php

use yii\db\Migration;

/**
 * Handles the creation of table `promo_record`.
 */
class m180806_054924_create_promo_record_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('promo_record', [
            'id' => $this->primaryKey()->comment('活动记录表主键id'),
            'promoId' => $this->integer()->notNull()->comment('活动id'),
            'userId' => $this->integer()->notNull()->comment('参与用户的id'),
            'note' => $this->string(50)->comment('描述信息'),
            'source' => $this->string(50)->comment('来源'),
            'quantity' => $this->integer()->notNull()->defaultValue(false)->comment('数量'),
            'isRead' => $this->boolean()->notNull()->defaultValue(false)->comment('是否已读 0未读 1已读'),
            'updateTime' => $this->timestamp()->comment('更新时间'),
            'createTime' => $this->timestamp()->null()->comment('创建时间'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('promo_record');
    }
}