<?php

use wap\modules\promotion\models\RankingPromo;
use yii\db\Migration;

class m170112_013744_alter_promo_table extends Migration
{
    public function up()
    {
        $transaction = \Yii::$app->db->beginTransaction();

        try {
            $this->addColumn('promo', 'startTime', $this->dateTime()->notNull());
            $this->addColumn('promo', 'endTime', $this->dateTime());

            $promos = RankingPromo::find()->all();

            foreach ($promos as $promo) {
                if (empty($promo->startAt)) {
                    throw new \Exception('活动开始时间不能为空');
                }

                $promo->startTime = date('Y-m-d H:i:s', $promo->startAt);

                if (!empty($promo->endAt)) {
                    $promo->endTime = date('Y-m-d H:i:s', $promo->endAt);
                }

                $promo->save(false);
            }

            $this->dropColumn('promo', 'startAt');
            $this->dropColumn('promo', 'endAt');

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();

            $this->dropColumn('promo', 'startTime');
            $this->dropColumn('promo', 'endTime');

            exit($e->getMessage());
        }
    }

    public function down()
    {
        echo "m170112_013744_alter_promo_table cannot be reverted.\n";

        return false;
    }
}
