<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 10:42
 */

namespace wap\modules\promotion\controllers;


use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use wap\modules\promotion\models\RankingPromoOfflineSale;
use yii\web\Controller;
use yii\web\Response;

class SaleController extends Controller
{
    /**
     * Ajax 请求获取投资排名
     * @param $id
     * @return array
     * @throws \yii\base\Exception
     */
    public function actionRanking($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $ranking = RankingPromo::find()->where(['id' => $id])->one();
        if (null === $ranking) {
            return ['code' => false, 'message' => '没有找到指定活动'];
        }
        //获取线上用户的投资金额排名前10
        $startAt = $ranking->startAt;
        $endAt = $ranking->endAt;
        $sql = "SELECT u.mobile ,SUM(o.order_money) AS totalInvest FROM `user` AS u LEFT JOIN online_order AS o ON u.id = o.uid WHERE o.status = 1 AND u.type = 1 AND (u.`created_at` BETWEEN :startAt AND :endAt) GROUP BY mobile ORDER BY totalInvest DESC LIMIT 10";
        $online = \Yii::$app->db->createCommand($sql, ['startAt' => $startAt, 'endAt' => $endAt])->queryAll();
        //获取线下用户投资金额排名前10
        $offline = RankingPromoOfflineSale::find()->select(['mobile', 'totalInvest'])->where(['rankingPromoOfflineSale_id' => $id])->orderBy(['totalInvest' => SORT_DESC])->limit(10)->asArray()->all();
        //合并两次排名，重新排名
        $rankingUser = array_merge_recursive($online, $offline);
        //排序
        usort($rankingUser, [RankingPromoOfflineSale::className(), 'rankingSort']);
        //截取前十，处理手机号和金额
        $rankingResult = RankingPromoOfflineSale::handleRankingResult(array_slice($rankingUser, 0, 10));
        return ['code' => true, 'data' => $rankingResult];
    }
}