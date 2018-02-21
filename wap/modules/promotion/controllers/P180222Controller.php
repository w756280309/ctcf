<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\Session;
use common\models\adv\ShareLog;
use common\models\promo\Question;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180222Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     * 答题初始页面
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180222']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
            'questions' => [],
        ];

        if ($isLoggedIn) {
            $joinTime = new \DateTime();
            $promoClass = new $promo->promoClass($promo);
            $data['questions'] = $promoClass->getQuestions($user, $joinTime);
        }
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 提交答案ACTION
     *
     * 提交方式：AJAX POST
     * 地址：/promotion/p180222/submit
     * 参数：
     *      qid：问题ID（number）
     *      opt：选项ID（number）
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,6,
     *          'message' => '成功，未开始，已结束，未登录，系统错误',
     *          'ticket' => null,
     *      ]
     *
     * @return array
     */
    public function actionSubmit()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180222']);
        $user = $this->getAuthedUser();
        $questionId = (int) Yii::$app->request->post('qid');
        $opt = (int) Yii::$app->request->post('opt');
        try {
            //判断状态
            $this->checkStatus($promo, $user);

            //获得正确答案
            $question = Question::findOne($questionId);
            if (null !== $question && $question->promoId === $promo->id) {
                $realOpt = (int) $question->answer;

                return [
                    'code' => 0,
                    'message' => $opt === $realOpt ? '恭喜您答对了！' : '您答错了，继续努力！',
                    'ticket' => $realOpt,
                ];
            } else {
                throw new \Exception('问题错误', self::ERROR_CODE_SYSTEM);
            }
        } catch (\Exception $ex) {
            $code = $ex->getCode();

            return $this->getErrorByCode($code);
        }
    }

    /**
     * 最后一次提交答案后自动触发（即交卷）ACTION
     *
     * 提交方式：AJAX POST
     * 地址：/promotion/p180222/finish
     * 参数：
     *      sn：批次号（string）
     *      res：答案信息（string）例： {"1":101,"2":102,"3":102,"4":-1,"5":104}
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,6,
     *          'message' => '成功，未开始，已结束，未登录，系统错误',
     *          'ticket' => null 或者 [
     *              'id' => 1,
     *              'sn' =>  '170828_p_77',
     *              'ref_type' => 'POINT',
     *              'ref_amount' => '77.00',
     *              'name' => 'iphone\\u624b\\u673a',
     *              'path' => 'wap\\/campaigns\\/active20170823\\/images\\/gifts_5.png'
     *          ],
     *      ]
     *
     * @return array
     */
    public function actionFinish()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180222']);
        $user = $this->getAuthedUser();
        $sn = Yii::$app->request->post('sn');
        $res = Yii::$app->request->post('res');
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $rewardInfo = $promoClass->finish($user, $sn, $res);

            return [
                'code' => 0,
                'message' => '成功',
                'rewardNum' => $rewardInfo['correctNum'],
                'ticket' => $rewardInfo['prize'],
            ];
        } catch (\Exception $ex) {
            $code = $ex->getCode();

            return array_merge($this->getErrorByCode($code), ['rewardNum' => 0]);
        }
    }

    /**
     * 开始答题ACTION
     */
    public function actionBegin()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180222']);
        $user = $this->getAuthedUser();
        try {
            //检查状态
            $this->checkStatus($promo, $user);

            //如果当前已有两条答题记录，则表明用户已完成今日任务，无答题机会
            $joinTime = new \DateTime();
            $sessionCount = (int) Session::findByCreateTime($user, $joinTime)->count();
            if (2 === $sessionCount) {
                throw new \Exception('今日答题机会已用完', self::ERROR_CODE_TODAY_NO_TICKET);
            }

            //如果用户没有分享记录且已经有过答题记录，则用户还有一次分享的答题机会
            $shareLog = ShareLog::fetchByConfig($user, 'timeline', 'p180222', $joinTime);
            if (1 === $sessionCount && null === $shareLog) {
                throw new \Exception('分享后可再答一次题', self::ERROR_CODE_NO_TICKET);
            }

            return [
                'code' => 0,
                'message' => '成功',
                'ticket' => null,
            ];
        } catch (\Exception $ex) {
            $code = (int) $ex->getCode();

            return $this->getErrorByCode($code);
        }
    }
}
