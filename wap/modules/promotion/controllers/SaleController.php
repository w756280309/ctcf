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
use yii\helpers\ArrayHelper;
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
        //获取线下用户投资金额排名前10
        $offline = $ranking->offline;
        //获取线下投资用户的线上投资数据
        $both = $ranking->both;
        //获取线上用户的投资金额排名前10
        $online = $ranking->online;
        //合并三次排名，重新排名
        $rankingUser = array_merge_recursive($online, $offline, $both);
        //相同手机号进行合并
        $rankingUser = $ranking->getMergeMobile($rankingUser);
        //排序
        usort($rankingUser, [RankingPromo::class, 'rankingSort']);
        //获取前十
        $ten = array_slice($rankingUser, 0, 10, true);
        //处理手机号和金额
        $rankingResult = RankingPromoOfflineSale::handleRankingResult($ten);
        return ['code' => true, 'data' => $rankingResult];
    }
}