<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\Session;
use common\models\promo\Question;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180321Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 初始化页面
     * questions: 本次答题的所有问题
     * id：单个问题的id，
     * title: 问题内容
     * batchSn: 此问题用户的批次
     * time: 剩余答题时间 0-30之间,用于前端记录答题剩余时间
     * sessionCount: 当前批次答题次数
     * requirePopRegular: 是否弹活动攻略，每天首次登录进入活动页面弹一次，未登录下一直弹，默认true弹出
     * result => [
     *      status : 1,  是否显示答题结果,当显示时，有数值，还在答题中时，answer为空
     *      answer: [
     *           ‘sn’: 批次号
     *           'count'： 答题正确次数
     *      ]
     * ]
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180321']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
            'promoStatus' => $this->getPromoStatus($promo),
            'questions' => [],
            'time' => 30,
            'sessionCount' => 0,
            'requirePopRegular' => true,
            'result' => [
                'status' => 0,
                'answer' => [],
            ],
        ];
        if (null !== $user) {
            $promoClass = new $promo->promoClass($promo);
            $joinTime = new \DateTime();
            $data['questions'] = $promoClass->getQuestions($user);
            $answerInfo = $promoClass->getAnswerInfo($user, $joinTime);
            $data['time'] = $answerInfo['restTime'];
            $data['result'] = $answerInfo['result'];
            $data['sessionCount'] = $promoClass->getSessionCount($user);
            $redis = Yii::$app->redis_session;
            $redisKey = 'P180321-'.$user->id;
            if ($redis->exists($redisKey)) {
                $data['requirePopRegular'] = false;
            } else {
                $redis->setex($redisKey, strtotime(date('Y-m-d').' 23:59:59') - time(), true);
            }
        }
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 获取答题接口
     *
     * - 兼容老版本APP需要
     */
    public function actionQuestions()
    {
        $questions = [];
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180321']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        if ($isLoggedIn) {
            $promoClass = new $promo->promoClass($promo);
            $questions = $promoClass->getQuestions($user);
        }

        return $questions;
    }

    /**
     * 开始挑战接口
     * @return array 返回值：
     * code：0,1,2,3,4,5
     * message:'成功','未开始',‘已结束’,'未登录','分享以后可再次答题,今日答题机会已用完'
     */
    public function actionBegin()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180321']);
        $user = $this->getAuthedUser();
        try {
            //检查状态
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $joinTime = new \DateTime();
            $promoClass->checkRestTicket($user, $joinTime);

            return [
                'code' => 0,
                'message' => '成功',
                'ticket' => null,
            ];
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if ($code > 5) {
                $code = 6;
            }

            return $this->getErrorByCode($code);
        }

        return $data;
    }

    /**
     * 答题接口（todo 无提交Action，是否为30后刷新页面？）
     *
     * @param int $qid 问题编号，为初始化页面中的id
     * @param int $opt 用户选择的答案
     *
     * @return array  返回值：
     * code: 0,1
     * message: '正确','错误'
     * count: 正确答案的个数
     */

    public function actionAnswer($qid, $opt)
    {
        $qid = (int) Yii::$app->request->get('qid');
        $opt = (int) Yii::$app->request->get('opt');
        $promo = RankingPromo::findOne(['key' => 'promo_180321']);
        $user = $this->getAuthedUser();

        try {
            //检查状态
            $this->checkStatus($promo, $user);
            $question = Question::findOne($qid);
            if (null === $question) {
                throw new \Exception('当前无此问题');
            }
            $promoClass = new $promo->promoClass($promo);
            $r = (int) $question->answer === $opt;
            if ($r) {
                $rewardCount = $promoClass->getCurrentRewardedCount($user);
                $currentCount = $rewardCount + 1;
                $sessionSn = 'batchNum' . $currentCount;
                Session::initNew($user, $sessionSn)->save(false);
            }
            $data = [
                'code' => $r ? 1 : 0,
                'message' => $r ? '正确' : '错误',
                'count' => $promoClass->getSessionCount($user),
            ];

            return $data;
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if ($code > 3) {
                $code = 6;
            }

            return $this->getErrorByCode($code);
        }
    }

    /**
     * 开启宝箱接口
     *
     * @return array 返回值：
     * code：0,1,2
     * message: '成功'，‘未开始’，‘已结束’
     * ticket:reward对象，没有奖品时为null
     */
    public function actionOpen()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180321']);
        $user = $this->getAuthedUser();
        try {
            //检查状态
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $reward = $promoClass->openAward($user);

            $data = [
                'code' => 0,
                'message' => '成功',
                'ticket' => $reward,
            ];

            return $data;
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();
            if (4 === $code) {
                Yii::$app->response->statusCode = 400;
                return [
                    'code' => 4,
                    'message' => '您已经开过宝箱了',
                    'ticket' => null,
                ];
            } elseif ($code > 4) {
                $code = 6;
            }

            return $this->getErrorByCode($code);
        }
    }
}
