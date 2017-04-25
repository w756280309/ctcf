<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use wap\modules\promotion\models\RankingPromoOfflineSale;
use yii\web\Controller;
use yii\web\Response;

class SaleController extends Controller
{
    private $cache_time = 600;//缓存时间，10分钟

    /**
     * Ajax 请求获取投资排名
     * @param $id
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionRanking($id)
    {
        $ranking = RankingPromo::find()->where(['id' => $id])->one();
        if (null === $ranking) {
            return ['code' => false, 'message' => '没有找到指定活动'];
        }
        $cache = \Yii::$app->cache;
        $key = ['promo', 'ranking', $id,];

        if ($cache->exists($key)) {
            return ['code' => true, 'data' => $cache->get($key)];
        }
        //获取线下用户投资金额排名前10
        $offline = $ranking->offline;
        //获取线下投资用户的线上投资数据
        $both = $ranking->both;
        //获取线上用户的投资金额排名前10
        $online = $ranking->online;
        //合并三次排名，重新排名
        $rankingUser = array_merge_recursive($online, $offline, $both);
        //相同手机号进行合并 这个排名活动了相同手机号的合并数据不准确 主要业务是处理online_order业务
        $rankingUser = $ranking->getMergeMobile($rankingUser);
        //排序
        usort($rankingUser, [RankingPromo::class, 'rankingSort']);
        //获取前十
        $ten = array_slice($rankingUser, 0, 10, true);
        //处理手机号和金额
        $rankingResult = RankingPromoOfflineSale::handleRankingResult($ten);
        $cache->set($key, $rankingResult, $this->cache_time);
        return ['code' => true, 'data' => $rankingResult];
    }
}