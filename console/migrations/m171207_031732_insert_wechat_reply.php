<?php

use yii\db\Migration;

class m171207_031732_insert_wechat_reply extends Migration
{
    public function up()
    {
        $this->insert('wechat_reply', [
            'type' => 'text',
            'keyword' => '答案',
            'content' => '人民币',
            'createdAt' => time(),
            'updatedAt' => time(),
        ]);
        $this->insert('wechat_reply', [
            'type' => 'image',
            'keyword' => '投票',
            'content' => 'Wf2CgM-J0s1Pp7DYngnxNTK6bn-86H2Qehm42uVHP0g',
            'createdAt' => time(),
            'updatedAt' => time(),
        ]);
        $this->insert('wechat_reply', [
            'type' => 'image',
            'keyword' => '网购',
            'content' => 'nN_rLRJV3maY4vKr2zyBy8ubZPmvmBLRl3DhT8KRI3I',
            'createdAt' => time(),
            'updatedAt' => time(),
        ]);
    }

    public function down()
    {
        echo "m171207_031732_insert_wechat_reply cannot be reverted.\n";

        return false;
    }
}
