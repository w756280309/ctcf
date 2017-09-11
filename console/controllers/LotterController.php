<?php

namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\promo\PromoPoker;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;
use common\models\promo\Poker;
use common\models\promo\PokerUser;

/**
 * 周周乐活动，定时开奖
 */
class LotterController extends Controller
{
    private $cardSn = 'poker_card200';
    private $pointSn = 'poker_point6_16';

    /**
     * 星期一0:00~10:00内执行
     */
    public function actionIndex()
    {
        $term = date('Ymd');
        /*
         * 获取上一周的开始时间，结束时间
         */
        $start = date('Y-m-d',strtotime('-1 monday')).' 00:00:00';
        $end = date('Y-m-d', strtotime($start) + 6 * 24 * 3600). ' 23:59:59';

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = New Poker();
            $model->term = $term;
            /*
             * 中奖号码第一位
             * 开奖前一周的总交易额 % 13
             */
            $model->spade = OnlineOrder::find()
                    ->where(['status' => 1])
                    ->andWhere(['between', 'created_at', strtotime($start), strtotime($end)])
                    ->sum('order_money') % 13;
            if ($model->spade == 0) {
                $model->spade += 13;
            }

            /*
             * 第二位，开奖前一周，登录用户数 % 13
             */
            $model->heart = PokerUser::find()
                    ->where(['between', 'createTime', $start, $end])
                    ->count() % 13;
            if ($model->heart == 0) {
                $model->heart += 13;
            }

            /*
             * 第三位，开奖前一期，签到用户数 % 13
             */
            $model->club = PokerUser::find()
                    ->where(['between', 'createTime', $start, $end])
                    ->andWhere(['>', 'club', 0])
                    ->count() % 13;
            if ($model->club == 0) {
                $model->club += 13;
            }

            /*
             * 第四位，开奖前一周，投资用户数 % 13
             */
            $model->diamond = PokerUser::find()
                    ->where(['between', 'createTime', $start, $end])
                    ->andWhere(['>', 'diamond', 0])
                    ->count() % 13;
            if ($model->diamond == 0) {
                $model->diamond += 13;
            }

            $tokenKey = 'poker_'.$term;
            TicketToken::initNew($tokenKey)->save(false);
            $model->save(false);
            $transaction->commit();
            $this->stdout("第".$model->term."期开奖成功，开奖号码：黑桃".$model->spade."，红桃".$model->heart."，梅花".$model->club."，方块".$model->diamond);
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->stdout("第".$model->term."期开奖失败。");
        }
    }

    /**
     * 星期一10:00执行
     */
    public function actionAward()
    {
        $term = date('Ymd');
        $promo = RankingPromo::find()
            ->where(['key' => 'promo_poker'])
            ->one();
        if (null === $promo) {
            $this->stdout('未找到周周乐活动');
            return false;
        }

        $PromoPoker = new PromoPoker($promo);
        $poker = Poker::find()
            ->where(['term' => $term])
            ->one();
        if (null === $poker) {
            $this->stdout('未生成'.$term.'期中奖号码');
            return false;
        }

        $data = PokerUser::find()
            ->where(['term' => $term])
            ->andWhere(['spade' => $poker->spade])
            ->all();

        $rewardCard = Reward::fetchOneBySn($this->cardSn);
        $rewardPoint = Reward::fetchOneBySn($this->pointSn);
        $num = 0;
        \Yii::info('[command][lotter/award]发奖开始-'.$term, 'command');
        $db = \Yii::$app->db;
        foreach ($data as $rewardInfo) {
            if (
                $rewardInfo->heart === $poker->heart
                && $rewardInfo->club === $poker->club
                && $rewardInfo->diamond === $poker->diamond
            ) {
                $reward = $rewardCard;
            } else {
                $reward = $rewardPoint;
            }

            $user = User::findOne($rewardInfo->user_id);
            if (null === $user) {
                continue;
            }
            $transaction = $db->beginTransaction();
            try {
                $tokenKey = $promo->id.'_'.$term.'_'.$user->id.'_'.$reward->id;
                TicketToken::initNew($tokenKey)->save(false);
                $PromoPoker->award($user, $reward);
                $transaction->commit();
            } catch (\Exception $ex) {
                //记录log
                $transaction->rollBack();
                \Yii::info('[command][lotter/award]发奖失败'.$tokenKey, 'command');
                continue;
            }
            $num++;
        }
        \Yii::info('[command][lotter/award]发奖结束-'.$term.'共计发奖为'.$num.'人', 'command');
        $this->stdout('共计发奖为'.$num.'人');
    }
}
