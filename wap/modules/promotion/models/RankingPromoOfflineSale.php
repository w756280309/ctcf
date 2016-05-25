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
 * @property string $investedAt
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
            [['rankingPromoOfflineSale_id', 'mobile', 'investedAt'], 'required'],
            [['rankingPromoOfflineSale_id'], 'integer'],
            [['totalInvest'], 'number'],
            [['mobile'], 'string', 'max' => 11, 'min' => 11],
            [['investedAt'], 'string'],
            [['mobile'], 'match', 'pattern' => '/^1[34578]\d{9}$/'],
            [['investedAt', 'rankingPromoOfflineSale_id'], 'validateInvestedAt'],
        ];
    }

    public function validateInvestedAt()
    {
        $ranking = RankingPromo::find()->where(['id' => $this->rankingPromoOfflineSale_id])->one();
        if (null === $ranking) {
            $this->addError('rankingPromoOfflineSale_id', '指定活动不存在');
        }
        $time = strtotime($this->investedAt);
        if ($time < $ranking->startAt || $time > $ranking->endAt) {
            $this->addError('investedAt', '投资时间不在活动时间内');
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rankingPromoOfflineSale_id' => '活动名称',
            'mobile' => '手机号',
            'totalInvest' => '总投资额（元）',
            'investedAt' => '投资时间',
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
            $result[] = ['mobile' => substr($v['mobile'], 0, 3) . '******' . substr($v['mobile'], 9, 2), 'totalInvest' => number_format($v['totalInvest'], 2)];
        }
        return $result;
    }
}
