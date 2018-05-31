<?php

namespace wap\modules\promotion\controllers;

use common\models\coupon\CouponType;
use common\models\promo\Award;
use common\models\promo\Reward;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use common\models\promo\Question;

class P180601Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     *  六一儿童节活动初始化页面
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isLoggedIn 判断登录状态，已登录：true,未登录：false
     * result => [
     *    'GLTales' => [      //  aesopFables:伊索寓言   chineseFairyTales:中国经典童话
     *          'name' => '格林童话',
     *          'isPassed' => [true, false]  //是否通关，true为通关，false为未通关
     *    ],
     *    'andersonTales' => [
     *          'name' => '安徒生童话',
     *          'isPassed' => [true, false]  //是否通关，true为通关，false为未通关
     *    ]
     * ]
     *
     *
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        $result = $this->getTales();
        if ($user) {
            $redis = Yii::$app->redis;
            foreach ($result as $key => $value) {
                if ($redis->hget('talesStatus', $key . $user->id) === 'success') {
                    $result[$key]['isPassed'] = true;
                }
            }
        }
        $data = [
            'isLoggedIn' => null !== $user,
            'promoStatus' => $this->getPromoStatus($promo),
            'csrf' => Yii::$app->request->csrfToken,
            'result' => $result,
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 开始答题接口
     *
     * 提交方式：AJAX get
     * 地址：/promotion/p180601/begin
     * 传入参数：sn   //sn=['GLTales','andersonTales','aesopFables','chineseFairyTales']
     * 返回值：
     *      [
     *           'code' => [0,1,2,3],
     *           'message' => ['进行中','未开始', '已结束', '未登录']
     *          'dailyAnswerCount' => [0,1,2]    //当天答题次数  0为还没有答过题 1分享后继续答题 2,当天机会已用完
     *           'isPassed' => true/false   //true:已通关  false:未通关
     *          'questions' => [
     *              [
     *                  'id' => 10,
     *                  'title' => '问题的题目1',
     *                  'sn' => 'GLTales0'
     *              ],
     *              [
     *                  'id' => 11,
     *                  'title' => '问题的题目2',
     *                  'sn' => 'GLTales0'
     *              ]
     *          ]
     *      ]
     *
     * @return array
     */
    public function actionBegin()
    {
        $sn = Yii::$app->request->get('sn');
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->checkStatus($promo, $user);
            $tales = $this->getTales();
            $talesKey = array_keys($tales);
            if (!in_array($sn, $talesKey)) {
                throw new \Exception('参数错误');
            }
            $promoClass = new $promo->promoClass($promo);
            $joinTime = new \DateTime();
            $promoClass->checkRestTicket($user, $joinTime);
            $isPassed = $tales[$sn]['isPassed'];
            $redis = Yii::$app->redis;
            if ($redis->hget('talesStatus', $sn . $user->id) === 'success') {
                $isPassed = true;
            }
            $questions = $promoClass->getQuestions($user, $sn);
            $transaction->commit();
            return [
                'code' => '0',
                'message' => '成功',
                'isPassed' => $isPassed,
                'questions' => $questions,
            ];

        } catch (\Exception $ex) {
            $transaction->rollBack();
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 答题接口
     * 提交方式：AJAX get
     * 接口地址：/promotion/p180601/answer
     * 输入参数:qid, opt, sn  //qid:题目id,  opt：用户选择的答案[A,B,C]  sn:童话故事标签
     * 返回字段：
     * {
     *      'code' => [0,1,2,3]
     *      'message' => ['进行中', '未开始', '已结束', '未登录']
     *      'isCorrect' => true/false  答案是否正确
     *      'rightAnswer' => [A,B,C]  //正确答案,
     *
     *
     *
    }
     * }
     */
    public function actionAnswer()
    {
        $qid = Yii::$app->request->get('qid');
        $opt = Yii::$app->request->get('opt');
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $question = Question::findOne($qid);
            if (null === $question) {
                throw new \Exception('当前无此问题');
            }
            $rightAnswer = $question->answer;
            $result = [
                'code' => 0,
                'message' => '错误',
                'isCorrect' => false,
                'rightAnswer' => $rightAnswer,
            ];
            if ($rightAnswer === $opt) {
                $result['message'] = '正确';
                $result['isCorrect'] = true;
            }

            return $result;
        } catch (\Exception $ex) {
            $code = $ex->getCode();

            return $this->getErrorByCode($code);
        }
    }

    /**
     * 发奖接口
     * 提交方式：ajax get
     * 接口地址：/promotion/p180601/open
     * 输入参数:sn    童话故事标签
     * 返回字段：
     * [
     *      code => [0,1,2,3],
     *      message => ['进行中', '未开始', '已结束', '未登录'
     *      'isPassed' => true/false   //是否通关  true:已通关  false：未通关
     *      'result' => [
     *          'sn' => '180601_C2',
     *          'name' => '2元代金券',
     *          'ref_amount' => 2,
 *              'ref_type' => 'COUPON',
     *      ]
     * ]
     */
    public function actionOpen()
    {
        $sn = Yii::$app->request->post('sn');
        $results = Yii::$app->request->post('results');
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $redis = Yii::$app->redis;
            $isPassed = false;
            if ($redis->hget('talesStatus', $sn . $user->id) === 'success') {
                $isPassed = true;
            }
            $result = [];
            $promoClass = new $promo->promoClass($promo);
            if (!$isPassed) {
                $awardRecord = $promoClass->getAwardRecord($sn, $results, $user);
                $isPassed = $awardRecord['isPassed'];
                $result = $awardRecord['amount'];
            } else {
                $promoClass->subtractOneTicket($user);

                return [
                    'code' => 4,
                    'message' => '您已通关,不能获取奖励',
                ];
            }

            return [
                'code' => 0,
                'message' => '成功',
                'isPassed' => $isPassed,
                'result' => $result,
            ];

        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getErrorByCode($code);
        }
    }

    /**
     * 奖品列表接口
     * @return array
     */
    public function actionAwardList()
    {
        $r = Reward::tableName();
        $a = Award::tableName();
        $c = CouponType::tableName();
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180601']);
        $user = $this->getAuthedUser();
        if ($user) {
            return Award::find()
                ->select("$r.name, $r.ref_type, $r.ref_amount, $r.path, $a.createTime as awardTime, $c.minInvest")
                ->innerJoin($r, "$r.id = $a.reward_id")
                ->innerJoin($c, "$r.ref_id = $c.id")
                ->where(["$a.user_id" => $user->id])
                ->andWhere(["$a.promo_id" => $promo->id])
                ->andWhere(["$a.ref_type" => 'coupon'])
                ->orderBy(["$a.createTime" => SORT_DESC])
                ->asArray()
                ->all();
        }
    }

    private function getTales()
    {
        return [
            'GLTales' => [
                'name' => '格林童话',
                'isPassed' => false,
            ],
            'andersonTales' => [
                'name' => '安徒生童话',
                'isPassed' => false,
            ],
            'aesopFables' => [
                'name' => '伊索寓言',
                'isPassed' => false,
            ],
            'chineseFairyTales' => [
                'name' => '中国经典童话',
                'isPassed' => false,
            ]
        ];
    }
}