<?php

use yii\db\Migration;

class m160524_092750_update_affiliate_campaign_table extends Migration
{
    public function up()
    {
        $this->alterColumn('affiliate_campaign', 'trackCode', $this->string()->unique());
    }

    public function down()
    {
        $this->alterColumn('affiliate_campaign', 'trackCode', $this->string());
    }
}
