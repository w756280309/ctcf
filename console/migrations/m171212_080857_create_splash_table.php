<?php

use yii\db\Migration;

/**
 * Handles the creation of table `splash`.
 */
class m171212_080857_create_splash_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('splash', [
            'id' => $this->primaryKey(),
            'title' => $this->char(60)->notNull()->comment('标题'),
            'sn' => $this->char(14)->notNull(),
            'img640x960' => $this->integer()->unsigned()->comment('640x960对应的mediaID'),
            'img640x1136' => $this->integer()->unsigned()->comment('640x1136对应的mediaID'),
            'img750x1334' => $this->integer()->unsigned()->comment('750x1334对应的mediaID'),
            'img1242x2208' => $this->integer()->unsigned()->comment('1242x2208对应的mediaID'),
            'img1080x1920' => $this->integer()->unsigned()->comment('1080x1920对应的mediaID'),
            'creator_id' => $this->integer()->comment('创建者ID'),
            'publishTime' => $this->dateTime()->comment('发布时间'),
            'isPublished' => $this->smallInteger()->unsigned()->comment('是否发布'),
            'createTime' => $this->integer()->unsigned()->notNull()->comment('创建时间'),
            'updateTime' => $this->integer()->unsigned()->comment('更新时间'),
            'auto_publish' => $this->smallInteger()->unsigned()->comment('是否自动发布'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('splash');
    }
}
