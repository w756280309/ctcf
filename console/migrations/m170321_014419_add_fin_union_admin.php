<?php

use common\models\affiliation\Affiliator;
use yii\db\Migration;

class m170321_014419_add_fin_union_admin extends Migration
{
    public function up()
    {
        $db = Yii::$app->db_fin;
        $affiliator = Affiliator::find()->where(['name' => 'O2O测试商家'])->one();
        if (null !== $affiliator) {
            $sql = "insert into admin(`loginName`, `passwordHash`, `affiliator_id`, `name`) values(:loginName, :passwordHash, :affiliatorId, :name)";
            $db->createCommand($sql, [
                'loginName' => 'testo2o',
                'passwordHash' => \Yii::$app->security->generatePasswordHash('a111111'),
                'affiliatorId' => $affiliator->id,
                'name' => $affiliator->name,
            ])->execute();
        }
    }

    public function down()
    {
        echo "m170321_014419_add_fin_union_admin cannot be reverted.\n";

        return false;
    }
}
