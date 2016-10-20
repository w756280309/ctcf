<?php

use yii\db\Migration;

class m161019_013414_update_issuer_table extends Migration
{
    public function up()
    {
        $this->insert('issuer', ['name' => '贵州省三都水族自治县国有资本营运有限责任公司']);
        $this->insert('issuer', ['name' => '北京北大高科技产业投资有限公司']);
        $this->insert('issuer', ['name' => '青州市城市建设投资开发有限公司']);
        $this->insert('issuer', ['name' => '泰通（泰州）工业有限公司']);
        $this->update('issuer', ['name' => '深圳立合旺通商业保理有限公司'], ['name' => '立合旺通']);
    }

    public function down()
    {
        echo "m161019_013414_update_issuer_table cannot be reverted.\n";

        return false;
    }
}
