<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "promo_sequence".
 *
 * @property integer $id
 */
class PromoSequence extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_sequence';
    }
}
