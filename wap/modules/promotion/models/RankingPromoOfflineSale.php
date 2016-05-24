<?php

namespace wap\modules\promotion\models;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ranking_promo_offline_sale".
 *
 * @property integer $id
 * @property integer $rankingPromoOfflineSale_id
 * @property string $mobile
 * @property string $totalInvest
 */
class RankingPromoOfflineSale extends ActiveRecord
{
    public static function tableName()
    {
        return 'ranking_promo_offline_sale';
    }

    public function rules()
    {
        return [
            [['rankingPromoOfflineSale_id', 'mobile'], 'required'],
            [['rankingPromoOfflineSale_id'], 'integer'],
            [['totalInvest'], 'number'],
            [['mobile'], 'string', 'max' => 11],
            [['mobile'], 'match', 'pattern' => '/^1[34578]\d{9}$/'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rankingPromoOfflineSale_id' => '活动名称',
            'mobile' => '手机号',
            'totalInvest' => '总投资额（元）',
        ];
    }

    public function getRankingPromo()
    {
        return $this->hasOne(RankingPromo::className(), ['id' => 'rankingPromoOfflineSale_id']);
    }

    /**
     * 处理手机号和金额
     * @param array $rankingData 待处理的数组
     * @return array
     * @throws Exception
     */
    public static function handleRankingResult($rankingData)
    {
        $result = [];
        foreach ($rankingData as $k => $v) {
            $result[] = ['mobile' => substr($k, 0, 3) . '******' . substr($k, 9, 2), 'totalInvest' =>number_format($v, 2)];
        }
        return $result;
    }
}
