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
     * 自定义排序回调函数
     * @param $a
     * @param $b
     * @return int
     * @throws Exception
     */
    public static function rankingSort($a, $b)
    {
        if (isset($a['totalInvest']) && isset($b['totalInvest'])) {
            if ($a['totalInvest'] < $b['totalInvest']) {
                return 1;
            } elseif ($a['totalInvest'] == $b['totalInvest']) {
                return 0;
            } else {
                return -1;
            }
        } else {
            throw new Exception('参数错误');
        }
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
            if (isset($v['mobile']) && isset($v['totalInvest'])) {
                $result[$k]['mobile'] = substr($v['mobile'], 0, 3) . '******' . substr($v['mobile'], 9, 2);
                $result[$k]['totalInvest'] = number_format($v['totalInvest'], 3);
            } else {
                throw new Exception('参数错误');
            }
        }
        return $result;
    }
}
