<?php

use yii\db\Migration;

class m170511_094021_alter_voucher extends Migration
{
    public function up()
    {
        $this->alterColumn('voucher', 'ref_type', $this->string());
        $this->alterColumn('voucher', 'ref_id', $this->string());
    }

    public function down()
    {
        echo "m170511_094021_alter_voucher cannot be reverted.\n";

        return false;
    }
}
