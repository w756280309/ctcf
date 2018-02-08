<?php

namespace wap\modules\promotion\controllers;

use common\models\adv\Session;
use common\models\adv\ShareLog;
use common\models\promo\Award;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180207Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 答题初始页面
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180207']);
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $isPopGameGuide = true;
        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => $isLoggedIn,
            'activeTicketCount' => 0,
            'questions' => [],
            'isPopGameGuide' => $isPopGameGuide,
        ];

        $joinTime = (new \DateTime());
        $promoClass = new $promo->promoClass($promo);
        if ($isLoggedIn) {
            $redis = Yii::$app->redis_session;
            if ($redis->hexists('isPopGameGuide', $user->id)) {
                $isPopGameGuide = $redis->hget('isPopGameGuide', $user->id);
            } else {
                //生存时间10天
                $redis->hset('isPopGameGuide', $user->id, false);
                $redis->expire('isPopGameGuide', 10 * 24 * 60 * 60);
            }
            $data['activeTicketCount'] = $promoClass->getTodayActiveTicketCount($user, $joinTime);
            $data['questions'] = $this->getQuestions($user, $joinTime);
            $data['isPopGameGuide'] = boolval($isPopGameGuide);
        }

        //将信息写入到对应的js变量中
        $this->renderJsInView($data);

        return $this->render('index');
    }


    private function getQuestions($user, $joinTime)
    {
        $sessionCount = (int) Session::findByCreateTime($user, $joinTime)->count();
        $sessionCount = $sessionCount >= 1 ? 1 : $sessionCount;
        $dateSn = $joinTime->format('Ymd') . $sessionCount;
        $questionData = require_once __DIR__.'/180205.php';
        $data = isset($questionData[$dateSn]) ? $questionData[$dateSn] : [];
        shuffle($data);

        return $data;
    }

    /**
     * 答题提交ACTION，返回中奖结果
     * //todo 暂时先不提出公共方法，表结构未设计完整
     *
     * 请求方式：POST
     * 地址: /promotion/p180207/reply
     * 参数：
     *      correctNum：正确条数
     *      sn：xxxxxx
     *
     * @return array
     */
    public function actionReply()
    {
        $correctNum = (int) Yii::$app->request->post('correctNum');
        $sn = Yii::$app->request->post('sn');
        $promo = RankingPromo::findOne(['key' => 'promo_180207']);
        $user = $this->getAuthedUser();

        //状态检查：用户活动及登录状态
        $a = $this->checkLoginAndPromo($promo, $user);
        if (!is_bool($a)) {
            return $a;
        }

        try {
            $promoClass = new $promo->promoClass($promo);
            $reward = $promoClass->reply($user, $correctNum, $sn);

            return $reward;
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            if (4 === $code) {
                return $this->getErrorByCode(self::ERROR_CODE_NO_TICKET);
            } else {
                return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
            }
        }
    }

    /**
     * 判断当前用户的状态及剩余游戏次数
     */
    public function actionWaste()
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180207']);
        $user = $this->getAuthedUser();
        $a = $this->checkLoginAndPromo($promo, $user);
        if (!is_bool($a)) {
            return $a;
        }

        //如果当前已有两条答题记录，则表明用户已完成今日任务，无答题机会
        $joinTime = new \DateTime();
        $sessionCount = (int) Session::findByCreateTime($user, $joinTime)->count();
        if (2 === $sessionCount) {
            return $this->getErrorByCode(self::ERROR_CODE_TODAY_NO_TICKET);
        }

        //如果用户没有分享记录且已经有过答题记录，则用户还有一次分享的答题机会
        $shareLog = ShareLog::fetchByConfig($user, 'timeline', 'p180207', $joinTime);
        if (1 === $sessionCount && null === $shareLog) {
            return $this->getErrorByCode(self::ERROR_CODE_NO_TICKET);
        }

        return [
            'code' => 0,
            'message' => '成功',
            'ticket' => null,
        ];
    }

    private function checkLoginAndPromo(RankingPromo $promo, $user)
    {
        try {
            $promo->isActive($user);
            //判断用户状态
            if (null === $user) {
                throw new \Exception('用户未登录', 3);
            }

            return true;
        } catch (\Exception $e) {
            $code = $e->getCode();
            if (!in_array($code, [1, 2, 3])) {
                return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
            }

            return $this->getErrorByCode($code);
        }
    }

    /**
     * 分享前请求Action
     */
    public function actionBeforeShare()
    {
        $data = [
            'totalAmount' => 0,
        ];
        $user = $this->getAuthedUser();
        $promo = RankingPromo::findOne(['key' => 'promo_180207']);
        if (null !== $user) {
            $data['totalAmount'] = (int) Award::findByPromoUser($promo, $user)
                ->sum('amount');
        }

        return $data;
    }

    public function actionAddShare($shareUrl, $scene)
    {
        $user = Yii::$app->user;
        $now = date("Y-m-d", time());
        $ipAddress = Yii::$app->request->getUserIP();
        $newShareLog = new ShareLog();
        $newShareLog->shareUrl = $shareUrl;
        $newShareLog->scene = $scene;
        $newShareLog->userId = $user->id;
        $newShareLog->ipAddress = $ipAddress;
        $newShareLog->createdAt = $now;
        $newShareLog->save(false);
    }
}
