<?php

use yii\db\Migration;

class m160518_065910_ranking_promo_offline_sale extends Migration
{
    public function up()
    {
        $this->createTable('ranking_promo_offline_sale', [
            'id' => $this->primaryKey(),
            'rankingPromoOfflineSale_id' => $this->integer()->notNull(),
            'mobile' => $this->string(11),
            'totalInvest' => $this->decimal()
        ]);
    }

    public function down()
    {
       $this->dropTable('ranking_promo_offline_sale');
    }
}
