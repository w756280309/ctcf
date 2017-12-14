<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\Award;
use common\models\promo\Reward;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\View;

class P171220Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    private $rewardStartDate = '2017-12-20';
    private $rewardEndDate = '2017-12-22';

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171220']);
        $user = $this->getAuthedUser();
        $isGuest = null === $user;
        $data  = [];
        $responseData = [
            'restTime' => $this->getRestTime($promo),
            'isLoggedIn' => !$isGuest,
            'myPoint' => $isGuest ? 0 : $user->points,
            'promoStatus' => $this->getPromoStatus($promo),
            'data' => $data,
        ];

        //获得要展示的商品 - 拼接数据
        $rewardQuery = Reward::find()
            ->where(['promo_id' => $promo->id]);
        $nowTime = time();
        $startTime = strtotime($this->rewardStartDate);
        $endTime = strtotime($this->rewardEndDate);
        if ($nowTime < $startTime) {
            $rewardQuery->andWhere(['date(createTime)' => $this->rewardStartDate]);
        } elseif ($nowTime > $endTime) {
            $rewardQuery->andWhere(['date(createTime)' => $this->rewardEndDate]);
        } else {
            $rewardQuery->andWhere(['date(createTime)' => date('Y-m-d')]);
        }
        $rewards = $rewardQuery->orderBy(['createTime' => SORT_ASC])->all();
        foreach ($rewards as $k => $reward) {
            $rewardAt = strtotime($reward->createTime);
            $data[$k]['name'] = $reward->name;
            $data[$k]['sn'] = $reward->sn;
            $data[$k]['timePoint'] = substr($reward->sn, -2);
            $data[$k]['currentPoints'] = (int) $reward->ref_amount;
            $data[$k]['restTime'] = $rewardAt - $nowTime >= 0 ? $rewardAt - $nowTime : 0;
            $data[$k]['allNum'] = null === $reward->limit ? 99999 : $reward->limit;
            $data[$k]['alreadyNum'] = $this->getSecKillNum($promo, $reward->id);
        }

        //当前小时数大于等于16点时，商品信息后面优先展示
        if (date('H') >= 16) {
            $data = array_reverse($data);
        }
        $responseData['data'] = $data;

        //写入到view层js的data变量里
        $responseData = json_encode($responseData);
        $view = Yii::$app->view;
        $js = <<<JS
var dataStr = '$responseData';
var datas = eval('(' + dataStr + ')');
JS;
        $view->registerJs($js, View::POS_HEAD);

        return $this->render('index');
    }

    /**
     * 获得距离下场的秒杀时间
     */
    private function getRestTime($promo)
    {
        //活动开始时间判断
        $nowTime = time();
        $startTime = strtotime($promo->startTime);
        if ($startTime > $nowTime) {
            return -1;
        }

        //最后一个秒杀开始时间判断
        $lastOpenAt = Reward::find()
            ->select('createTime')
            ->where(['promo_id' => $promo->id])
            ->orderBy(['createTime' => SORT_DESC])
            ->limit(1)
            ->scalar();
        if (!$lastOpenAt) {
            return 0;
        }
        $lastOpenTime = strtotime($lastOpenAt);
        if ($lastOpenTime <= $nowTime) {
            return 0;
        }

        //最近一次秒杀时间判断
        $recentOpenAt = Reward::find()
            ->select('createTime')
            ->where(['promo_id' => $promo->id])
            ->andFilterWhere(['>', 'createTime', date('Y-m-d H:i:s')])
            ->orderBy(['createTime' => SORT_ASC])
            ->limit(1)
            ->scalar();
        if (!$recentOpenAt) {
            return 0;
        }
        $recentOpenTime = strtotime($recentOpenAt);

        return $recentOpenTime - $nowTime;
    }

    /**
     * 当前用户获得该活动中某一秒杀商品的数量
     */
    private function getSecKillNum($promo, $rewardId)
    {
        $user = $this->getAuthedUser();
        if (null === $user) {
            return 0;
        }

        return (int) Award::find()
            ->where(['promo_id' => $promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['reward_id' => $rewardId])
            ->count();
    }

    /**
     * 秒杀Action
     */
    public function actionKill()
    {
        //判断活动参数
        $sn = Yii::$app->request->get('sn');
        $promo = RankingPromo::findOne(['key' => 'promo_171220']);
        if (empty($sn) || null === $promo) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断活动状态
        $promoStatus = null;
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        if (null !== $promoStatus) {
            return 1 === $promoStatus
                ? $this->getErrorByCode(self::ERROR_CODE_NOT_BEGIN)
                : $this->getErrorByCode(self::ERROR_CODE_ALREADY_END);
        }

        //判断登录状态
        if (null === $user) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        //秒杀
        try {
            $promoClass = new $promo->promoClass($promo);
            $killTime = new \DateTime();
            $ticket = $promoClass->secKill($user, $sn, $killTime);
            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '秒杀成功',
                'ticket' => $ticket,
            ];
        } catch (\Exception $ex) {
            if (in_array($ex->getCode(), [4, 5, 6, 7, 8])) {
                return [
                    'code' => $ex->getCode(),
                    'message' => $ex->getMessage(),
                    'ticket' => null,
                ];
            }

            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }
    }
}
