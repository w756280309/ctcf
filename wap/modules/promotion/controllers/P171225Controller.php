<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use Yii;

class P171225Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171225']);
        $isShowRegular = 1;
        $data = [
            'activeDrawnCount' => 0,
            'annualInvest' => 0,
            'grade' => 0,
            'isShowRegular' => $isShowRegular,
        ];
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $data = $this->getPageContent($promo, $user);
            //弹策略框逻辑(弹出过一次后不再弹出)
            $redis = Yii::$app->redis_session;
            if ($redis->hexists('effectPopRegular', $user->id)) {
                $isShowRegular = $redis->hget('effectPopRegular', $user->id);
            } else {
                $redis->hset('effectPopRegular', $user->id, 0);
                $redis->expire('effectPopRegular', 7 * 24 * 60 * 60);
            }
            $data['isShowRegular'] = $isShowRegular;
        }

        $this->registerPromoStatusInView($promo);

        return $this->render('index', ['data' => $data]);
    }

    /**
     * 判断当前是否可以解锁
     */
    public function actionUnlock()
    {
        $grade = (int) Yii::$app->request->get('grade');
        if ($grade > 5 || $grade < 0) {
            return $this->getCurrentError(self::ERROR_CODE_SYSTEM);
        }

        $promo = RankingPromo::findOne(['key' => 'promo_171225']);
        $user = $this->getAuthedUser();
        try {
            $this->getStatus($promo, $user);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getCurrentError($code);
        }
        $pageContent = $this->getPageContent($promo, $user, $grade);
        $realGrade = $pageContent['realGrade'];
        if ($realGrade < $grade) {
            return [
                'code' => 4,
                'message' => '解锁失败',
                'page' => $pageContent,
            ];
        }

        return [
            'code' => 0,
            'message' => '解锁成功',
            'page' => $pageContent,
        ];
    }

    private function getStatus($promo, $user)
    {
        $promo->isActive($user);
        if (null === $user) {
            throw new \Exception('未登录', self::ERROR_CODE_NOT_LOGIN);
        }

        return true;
    }

    /**
     * 判断当前礼包是否可以拆开
     */
    public function actionUnpack()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_171225']);
        $user = $this->getAuthedUser();
        try {
            $this->getStatus($promo, $user);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            return $this->getCurrentError($code);
        }
        $pageContent = $this->getPageContent($promo, $user);
        $activeDrawnCount = $pageContent['activeDrawnCount'];
        if ($activeDrawnCount > 0) {
            return [
                'code' => 0,
                'message' => '礼包打开成功',
                'page' => $pageContent,
            ];
        }

        return [
            'code' => 4,
            'message' => '无抽奖机会',
            'page' => [],
        ];
    }

    private function getPageContent($promo, $user, $grade = null)
    {
        $promoClass = new $promo->promoClass($promo);
        $tmpGrade = null;
        $annualInvest = $promoClass->getAnnualInvest($user);
        $realGrade = $promoClass->getCurrentGrade($annualInvest);
        $activeDrawnCount = $promoClass->getActiveTicketCount($user);
        if (null === $grade) {
            $grade = $realGrade;
            $tmpGrade = $realGrade + 1;
        } else {
            $tmpGrade = $grade;
        }
        $rewards = $promoClass->getHitRewardsByGrade($grade);
        $deficiencyAnnual = $promoClass->getDeficiencyAnnual($tmpGrade, $annualInvest);

        return array_merge([
            'activeDrawnCount' => $activeDrawnCount,
            'annualInvest' => rtrim(rtrim(bcdiv($annualInvest, 10000, 2), '0'), '.'),
            'grade' => $grade,
            'realGrade' => $realGrade,
            'deficiencyAnnual' => rtrim(rtrim(bcdiv($deficiencyAnnual, 10000, 2), '0'), '.'),
        ], $rewards);
    }

    private function getCurrentError($code)
    {
        Yii::$app->response->statusCode = 400;
        $allErrors = $this->getAllErrors();
        if (!isset($allErrors[$code])) {
            return $allErrors[self::ERROR_CODE_SYSTEM];
        }

        return $allErrors[$code];
    }

    private function getAllErrors()
    {
        return [
            self::ERROR_CODE_NOT_BEGIN => [
                'code' => 1,
                'message' => '活动未开始',
                'page' => [],
            ],
            self::ERROR_CODE_ALREADY_END => [
                'code' => 2,
                'message' => '活动已结束',
                'page' => [],
            ],
            self::ERROR_CODE_NOT_LOGIN => [
                'code' => 3,
                'message' => '未登录',
                'page' => [
                    'activeDrawnCount' => 0,
                    'annualInvest' => 0,
                    'grade' => 0,
                    'isShowRegular' => 1,
                ],
            ],
            self::ERROR_CODE_SYSTEM => [
                'code' => 6,
                'message' => '系统异常，请稍后测试',
                'page' => [],
            ],
        ];
    }
}
