<?php

use common\models\adv\Adv;
use common\models\media\Media;
use yii\db\Migration;

class m170213_093820_alter_adv_table extends Migration
{
    public function up()
    {
        $this->dropColumn('adv', 'pos_id');
        $this->dropColumn('adv', 'smallImage');
        $this->dropColumn('adv', 'largeImage');
        $this->addColumn('adv', 'media_id', $this->integer()->notNull());

        $advs = Adv::find()->all();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($advs as $adv) {
                if (Adv::TYPE_KAIPING === $adv->type) {
                    $type = 'jpg' === substr($adv->image, -3) ? 'image/jpeg' : 'image/png';
                    $uri = $adv->image;
                } else {
                    $type = 'image/jpeg';
                    $uri = 'upload/adv/'.$adv->image;
                }

                $media = Media::initNew($type, $uri);
                $media->save(false);

                $adv->media_id = $media->id;
                $adv->save(false);
            }

            $transaction->commit();
            $this->dropColumn('adv', 'image');

            echo '数据同步成功!';
        } catch (\Exception $e) {
            $transaction->rollBack();

            die('数据同步失败, 失败原因: '.$e->getMessage());
        }
    }

    public function down()
    {
        echo "m170213_093820_alter_adv_table cannot be reverted.\n";

        return false;
    }
}
