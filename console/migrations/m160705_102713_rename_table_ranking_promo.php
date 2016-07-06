<?php

use yii\db\Migration;

class m160705_102713_rename_table_ranking_promo extends Migration
{
    public function up()
    {
        $sql = "RENAME TABLE ranking_promo TO promo";
        Yii::$app->db->createCommand($sql)->execute();
    }

    public function down()
    {
        echo "m160705_102713_rename_table_ranking_promo cannot be reverted.\n";

        return false;
    }
}
