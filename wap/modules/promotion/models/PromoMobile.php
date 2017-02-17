<?php

namespace wap\modules\promotion\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "promo_mobile".
 *
 * @property integer $id
 * @property integer $promo_id
 * @property string $mobile
 * @property string $ip
 * @property string $createTime
 */
class PromoMobile extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promo_mobile';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['promo_id', 'mobile', 'createTime'], 'required'],
            [['promo_id'], 'integer'],
            [['createTime'], 'safe'],
            [['mobile', 'ip'], 'string', 'max' => 255],
        ];
    }

    public static function initNew($promoId, $mobile)
    {
        return new self([
            'promo_id' => $promoId,
            'mobile' => $mobile,
            'ip' => Yii::$app->request->userIP,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }
}
