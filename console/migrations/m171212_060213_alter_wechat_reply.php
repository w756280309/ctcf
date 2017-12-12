<?php

use yii\db\Migration;

class m171212_060213_alter_wechat_reply extends Migration
{
    public function up()
    {
        $this->addColumn('wechat_reply', 'style', $this->string()->notNull()->defaultValue(null)->comment('消息类型'));
        $this->alterColumn('wechat_reply', 'type', $this->string()->notNull()->defaultValue(null)->comment('回复类型'));
        $this->alterColumn('wechat_reply', 'keyword', $this->string()->notNull()->defaultValue(null)->comment('关键字'));
        $this->alterColumn('wechat_reply', 'content', $this->text()->notNull()->defaultValue(null)->comment('内容'));
    }

    public function down()
    {
        echo "m171212_060213_alter_wechat_reply cannot be reverted.\n";

        return false;
    }
}
