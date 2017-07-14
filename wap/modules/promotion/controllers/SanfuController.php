<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\exception\NotActivePromoException;
use common\models\promo\DogDays;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Controller;

class SanfuController extends Controller
{
    use HelpersTrait;

    public $layout = '@app/views/layouts/fe';
    private $promo = null;
    private $promoKey = 'promo_170717';

    public function actionIndex()
    {
        $promo = $this->promo($this->promoKey);

        $restCount = 0;
        $promoStatus = 0;
        $drawList = [];
        $requireRaise = false;
        $hasOrderTicket = null;
        $promoClass = new DogDays($promo);
        $user = $this->getAuthedUser();

        //活动状态
        try {
            $promo->isActive($user);
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }

        if ($user) {
            try {
                $promoClass->addPromoTicket($user, $promoClass::SOURCE_FREE);
            } catch (\Exception $ex) {
            }
            $drawList = $promoClass->getDrawnList($user);
            $restCount = $promoClass->getActiveTicketCount($user);
            $nowTime = new \DateTime();
            $requireRaise = $promoClass->requireRaisePool($user, $nowTime);
            $hasOrderTicket = PromoLotteryTicket::findLotteryByPromoId($promo->id)
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['source' => 'order'])
                ->andWhere(['date(from_unixtime(created_at))' => date('Y-m-d')])
                ->one();
        }

        return $this->render('index', [
            'user' => $user,
            'restCount' => $restCount,
            'drawList' => $drawList,
            'requireRaise' => $requireRaise,
            'promoStatus' => $promoStatus,
            'hasOrderTicket' => $hasOrderTicket,
        ]);
    }

    public function actionDraw()
    {
        $promo = $this->promo($this->promoKey);
        $user = $this->getAuthedUser();
        $nowTime = new \DateTime();
        try {
            $ticket = PromoService::draw($promo, $user);

            return [
                'code' => 0,
                'message' => '抽奖成功',
                'data' => [
                    'name' => $ticket->reward->name,
                    'imageUrl' => $ticket->reward->path,
                ],
            ];
        } catch (NotActivePromoException $ex) {
            Yii::$app->response->statusCode = 400;
            $code = $ex->getCode();
            if (in_array($code, [1, 2, 3])) {
                return [
                    'code' => $code,
                    'message' => $ex->getMessage(),
                ];
            } else {
                $subKey = $user->id . '-' . $promo->id . '-' . $nowTime->format('Ymd') . '-';
                $isShared = $this->isExistToken($subKey . DogDays::SOURCE_SHARE);
                $isInvested = $this->isExistToken($subKey . DogDays::SOURCE_ORDER);
                if (!$isShared) {
                    $code = 4;
                } elseif (!$isInvested) {
                    $code = 5;
                } elseif ($isShared && $isInvested) {
                    $code = 6;
                }

                return [
                    'code' => $code,
                    'message' => '无抽奖机会',
                ];
            }
        } catch (\Exception $ex) {
            Yii::$app->response->statusCode = 400;
            return [
                'code' => 7,
                'message' => $ex->getMessage(),
            ];
        }
    }

    public function actionAddShare()
    {
        $data = [
            'code' => 0,
            'message' => '操作成功',
        ];
        $user = $this->getAuthedUser();
        if (null === $user) {
            $data['code'] = 1;
            $data['message'] = '未登录';
        } else {
            $promo = $this->promo($this->promoKey);
            $promoClass = new DogDays($promo);
            try {
                $promoClass->addPromoTicket($user, $promoClass::SOURCE_SHARE);
            } catch (\Exception $ex) {
                $data['code'] = 1;
                $data['message'] = '操作失败';
            }
        }

        return $data;
    }

    /**
     * 获取活动信息.
     */
    private function promo($key)
    {
        return $this->promo ?: $this->findOr404(RankingPromo::class, ['key' => $key]);
    }

    /**
     * 查询是否已发了某种类型/或多种类型的抽奖机会
     */
    private function isExistToken($key)
    {
        return null !== TicketToken::findOne(['key' => $key]);
    }
}
