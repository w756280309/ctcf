<?php

namespace wap\modules\promotion\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ranking_promo".
 *
 * @property integer $id
 * @property string $title
 * @property integer $startAt
 * @property integer $endAt
 */
class RankingPromo extends ActiveRecord
{
    public static function tableName()
    {
        return 'ranking_promo';
    }

    public function rules()
    {
        return [
            [['title', 'startAt', 'endAt'], 'required'],
            [['startAt', 'endAt'], 'string'],
            [['title'], 'string', 'max' => 50],
            ['endAt', 'compare', 'compareAttribute' => 'startAt', 'operator' => '>']
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '活动名称',
            'startAt' => '开始时间',
            'endAt' => '结束时间',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (is_string($this->startAt)) {
                $this->startAt = strtotime($this->startAt);
            }
            if (is_string($this->endAt)) {
                $this->endAt = strtotime($this->endAt);
            }
            return true;
        } else {
            return false;
        }
    }

    //获取线下用户全部投资金额
    public function getOffline()
    {
        //获取线下用户
        $offline = RankingPromoOfflineSale::find()->select(['mobile', 'totalInvest', 'UNIX_TIMESTAMP(investedAt) as `time`'])->where(['rankingPromoOfflineSale_id' => $this->id])->orderBy(['totalInvest' => SORT_DESC, 'investedAt' => SORT_ASC])->asArray()->all();
        return $offline;
    }

    //获取线下投资用户的线上投资数据
    public function getBoth()
    {
        $offline = $this->offline;
        $result = [];
        if (count($offline) > 0) {
            $mobiles = '(\'' . implode('\',\'', ArrayHelper::getColumn($offline, 'mobile')) . '\')';
            $sql = "SELECT mobile ,SUM(order_money) AS totalInvest, MAX(order_time) AS `time`
                    FROM `online_order` AS o
                    WHERE o.status = 1
                    AND o.`mobile` IN " . $mobiles . "
                    AND o.`created_at` BETWEEN :startAt AND :endAt
                    GROUP BY mobile ORDER BY totalInvest DESC LIMIT 10 ";
            $result = \Yii::$app->db->createCommand($sql, ['startAt' => $this->startAt, 'endAt' => $this->endAt])->queryAll();
        }
        return $result;
    }

    //获取线上用户的投资金额排名前10
    public function getOnline()
    {
        $sql = "SELECT mobile ,SUM(order_money) AS totalInvest, MAX(order_time) AS `time`
                FROM `online_order` AS o
                WHERE o.status = 1
                AND o.`created_at` BETWEEN :startAt AND :endAt
                GROUP BY mobile ORDER BY totalInvest DESC LIMIT 10 ";
        $online = \Yii::$app->db->createCommand($sql, ['startAt' => $this->startAt, 'endAt' => $this->endAt])->queryAll();
        return $online;
    }

    //根据手机号对数据进行合并
    public function getMergeMobile($data)
    {
        $result = [];
        $key = [];
        if (count($data) > 0) {
            foreach ($data as $v) {
                if (isset($v['mobile']) && isset($v['totalInvest'])) {
                    $mobile = $v['mobile'];
                    if (in_array($mobile, $key)) {
                        $result[$mobile] = ['mobile' => $mobile, 'totalInvest' => $v['totalInvest'] + $result[$mobile]['totalInvest'], 'time' => max($v['time'], $result[$mobile]['time'])];
                    } else {
                        $result[$mobile] = ['mobile' => $mobile, 'totalInvest' => $v['totalInvest'], 'time' => $v['time']];
                        $key[] = $mobile;
                    }
                }
            }
        }
        return $result;
    }

    public static function rankingSort($a, $b)
    {
        if ($a['totalInvest'] > $b['totalInvest']) {
            return -1;
        } elseif ($a['totalInvest'] < $b['totalInvest']) {
            return 1;
        } else {
            if ($a['time'] > $b['time']) {
                return 1;
            } else {
                return -1;
            }
        }
    }
}
