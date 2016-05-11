<?php

use yii\db\Migration;

class m160510_170802_create_AffiliateCampaign_table extends Migration
{
    public function up()
    {
        $this->createTable('affiliate_campaign', [
            'id' => $this->primaryKey(),
            'trackCode' => $this->string(),
            'affiliator_id' => $this->integer()
        ]);
    }

    public function down()
    {
        $this->dropTable('affiliate_campaign');
    }
}
