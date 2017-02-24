<?php

use yii\db\Migration;

class m170223_030025_insert_sms_config_table extends Migration
{
    public function up()
    {
        $dateTime = date('Y-m-d H:i:s');

        $this->insert('sms_config', [
            'template_id' => '155514',
            'config' => json_encode([
                '1400',
                'https://m.wenjf.com/',
            ]),
            'createTime' => $dateTime,
        ]);

        $this->insert('sms_config', [
            'template_id' => '155515',
            'config' => json_encode([
                '2000',
                '沃尔玛100元超市卡',
                'https://m.wenjf.com/',
            ]),
            'createTime' => $dateTime,
        ]);

        $this->insert('sms_config', [
            'template_id' => '155661',
            'config' => json_encode([
                '288元',
                'http://dwz.cn/43x1YL',
                '400-101-5151',
            ]),
            'createTime' => $dateTime,
        ]);
    }

    public function down()
    {
        echo "m170223_030025_insert_sms_config_table cannot be reverted.\n";

        return false;
    }
}
