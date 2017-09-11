<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\promo\Award;
use common\models\promo\Poker;
use common\models\promo\PokerUser;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\Pagination;

class PokerController extends BaseController
{
    use HelpersTrait;
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $data = [];
        /*设置当前期数*/
        $nowAt = time();
        $term = PokerUser::calcTerm($nowAt);

        /*设置距离开奖时间的剩余秒数*/
        //获取本周一10点
        $rewardOpenTime = date('Y-m-d', strtotime('this week')).' 10:00:00';
        //获取下周一10点
        $rewardNextOpenTime = date('Y-m-d', strtotime('next week')).' 10:00:00';
        $isOpenReward = $nowAt > strtotime($rewardOpenTime);
        $restSecond = $isOpenReward
            ? strtotime($rewardNextOpenTime) - $nowAt
            : strtotime($rewardOpenTime) - $nowAt;

        /*设置当前用户卡片（未登录时为4个背面）*/
        $card = [0, 0, 0, 0];

        /*设置是否需要弹窗-弹获奖信息*/
        $requirePop = false;

        /*设置上一期默认期数及默认中奖号码（无上一期）*/
        $rewardCard = [0, 0, 0, 0];
        $qishu = null;
        //获取上一周中奖号码
        $poker = Poker::find()
            ->where(['<', 'term', $term])
            ->orderBy(['term' => SORT_DESC])
            ->limit(1)
            ->one();
        if (null !== $poker) {
            $rewardCard = [$poker->spade, $poker->heart, $poker->club, $poker->diamond];
            $qishu = $poker->term;
        }

        //设置默认未登录下rewardInfo的内容
        $rewardInfo = [
            'status' => 0,
            'rewardCard' => $rewardCard,
            'userCard' => $card,
            'qishu' => $qishu,
            'level' => null,
            'title' => null,
        ];

        $user = $this->getAuthedUser();
        if (null !== $user) {

            //判断如果session存的有效截止时间小于当前时间，则直接删除该键
            $effectPopRewardTime = Yii::$app->session->get('effectPop');
            if (null !== $effectPopRewardTime) {
                $popRewardAt = strtotime($effectPopRewardTime);
                if ($nowAt > $popRewardAt) {
                    Yii::$app->session->remove('effectPop');
                }
            }

            $currentCard = PokerUser::find()
                ->select(['spade', 'heart', 'club', 'diamond'])
                ->where(['user_id' => $user->id])
                ->andWhere(['term' => $term])
                ->one();

            if (!empty($currentCard)) {
                $card = [$currentCard->spade, $currentCard->heart, $currentCard->club, $currentCard->diamond];
                $card = array_map(function($value) {
                    return $this->showPokerValue($value);
                }, $card);
            }

            $record = $this->actionRecord(1, 1);
            $rewardInfo = isset($record['data'][0]) ? $record['data'][0] : $rewardInfo;
            $redis = Yii::$app->redis_session;
            if ($redis->hexists('effectPop', $user->id)) {
                $expireAt = $redis->hget('effectPop', $user->id);
                if ($nowAt >= $expireAt) {
                    $redis->hdel('effectPop', $user->id);
                }
            }

            if (!empty($rewardInfo) && 1 === $rewardInfo['status'] && !$redis->hexists('effectPop', $user->id)) {
                $requirePop = true;
                $redis->hset('effectPop', $user->id, strtotime($rewardNextOpenTime));
            }
        }
        $data = [
            'qishu' => $term,
            'restSecond' => $restSecond,
            'card' => $card,
            'requirePop' => $requirePop,
            'rewardInfo' => $rewardInfo,
        ];

        return $this->render('index', [
            'data' => json_encode($data),
        ]);
    }

    public function actionHistory()
    {
        //取第一页的前三条历史记录
        $record = $this->actionRecord();
        $data = $record['data'];

        //获得总页码数
        $totalCount = Poker::find()->count();
        $totalPage = ceil($totalCount / 3);

        //获得该用户所有参加过该活动的记录
        return $this->render('history', [
            'data' => json_encode($data),
            'totalPage' => $totalPage,
        ]);
    }

    public function actionRecord($page = 1, $limit = 3)
    {
        //判断是否登录
        $user = $this->getAuthedUser();
        if (null === $user) {
            return [];
        }

        //判断活动
        $promo = RankingPromo::find()
            ->where(['key' => 'promo_poker'])
            ->one();
        if (null === $promo) {
            return [];
        }

        //查询所有已开的中奖期数
        $data = [];
        $query = Poker::find()->where(['<', 'term', Poker::calcTerm(time())]);
        $cQuery = Clone $query;
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $limit]);
        $pokers = $cQuery->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy(['term' => SORT_DESC])
            ->all();

        //循环所有中奖期数,拼接返回值数组
        foreach ($pokers as $k => $poker) {
            $pokerUser = PokerUser::find()
                ->where(['term' => $poker->term])
                ->andWhere(['user_id' => $user->id])
                ->one();
            $rewardCard = [$poker->spade, $poker->heart, $poker->club, $poker->diamond];
            $rewardCard = array_map(function($value) {
                return $this->showPokerValue($value);
            }, $rewardCard);
            if (null === $pokerUser) {
                $data[$k] = [
                    'status' => 2,
                    'rewardCard' => $rewardCard,
                    'userCard' => [0, 0, 0, 0],
                    'qishu' => $poker['term'],
                    'level' => '未中奖',
                    'title' => '未中奖',
                ];
            } else {
                $userCard = [$pokerUser->spade, $pokerUser->heart, $pokerUser->club, $pokerUser->diamond];
                $userCard = array_map(function($value) {
                    return $this->showPokerValue($value);
                }, $userCard);
                if ($pokerUser->spade !== $poker->spade) {
                    $data[$k] = [
                        'status' => 2,
                        'rewardCard' => $rewardCard,
                        'userCard' => $userCard,
                        'qishu' => $poker['term'],
                        'level' => '未中奖',
                        'title' => '未中奖',
                    ];
                } else {
                    $title = $rewardCard === $userCard ? '200元超市卡' : '奖品10分钟到账';
                    $level = $rewardCard === $userCard ? '一等奖' : '幸运奖';
                    $award = Award::find()
                        ->where(['user_id' => $user->id])
                        ->andWhere(['promo_id' => $promo->id])
                        ->andWhere(['date(createTime)' => $poker['term']])
                        ->one();
                    if (null !== $award) {
                        $title = $level === '幸运奖' ? $award->amount.'积分' : '200元超市卡';
                    }
                    $data[$k] = [
                        'status' => 1,
                        'rewardCard' => $rewardCard,
                        'userCard' => $userCard,
                        'qishu' => $poker['term'],
                        'level' => $level,
                        'title' => $title,
                    ];
                }
            }
        }

        return [
            'data' => $data,
        ];
    }

    /**
     * 将1转为A,11转为J,12转为Q,13转为K
     */
    private function showPokerValue($value)
    {
        $pokerValue = 0;
        if (1 === $value) {
            $pokerValue = 'A';
        } elseif (11 === $value) {
            $pokerValue = 'J';
        } elseif (12 === $value) {
            $pokerValue = 'Q';
        } elseif (13 === $value) {
            $pokerValue = 'K';
        } else {
            $pokerValue = $value;
        }

        return $pokerValue;
    }
}
