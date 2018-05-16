<?php
namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use common\models\promo\TicketToken;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use yii;

class P180520Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /*
     * 520主活动会场 初始化页面
     * promoRedStatus 红包雨活动状态
     * promoCharityStatus 慈善活动抽奖状态
     * isLoggedIn 是否登录
     * isDrawDuration 本轮红包雨是否抽奖
     * medalCount 慈善勋章数量
     * now 返回时间戳
     * rewards 本次活动奖品
     * */
    public function actionIndex()
    {
        $user = $this->getAuthedUser();
        $isLoggedIn = false;
        $isDrawDuration = false;
        $medalCount = 0;
        $packetPromo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180516']);
        $charityPromo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180520']);

        if (null !== $user) {
            $isLoggedIn = true;
            $packetClass = new $packetPromo->promoClass($packetPromo);
            $isDrawDuration = $packetClass->getDrawDuration($user) == 1 ? true : false;
            $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180508']);
            $promoClass = new $promo->promoClass($promo);
            $medalCount = $promoClass->getMedalCount($user);
        }

        $data = [
            'promoRedStatus' => $this->getPromoStatus($packetPromo),
            'promoCharityStatus' => $this->getPromoStatus($charityPromo),
            'isLoggedIn' => $isLoggedIn,
            'isDrawDuration' => $isDrawDuration,
            'medalCount' => $medalCount,
            'now' => time()*1000,
            'startTime' => strtotime($packetPromo->startTime)*1000,
            'endTime' => strtotime($packetPromo->endTime)*1000,
        ];
        $this->renderJsInView($data);
        return $this->render('index');
    }

    /*
     * 红包雨抽奖接口
     * @return array
     * */
    public function actionGetPacketDraw()
    {
        $user = $this->getAuthedUser();
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180516']);
        $promoClass = new $promo->promoClass($promo);
        try {
            $this->checkStatus($promo, $user);
            $promoClass->checkDraw($user);
            $key = $promo->id . '-' . $user->id . '-' . date('dH');
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $promo, 'free')->save(false);
            $ticket = PromoService::draw($promo, $user);
            return [
                'code' => 0,
                'message' => '成功',
                'sn' => $ticket->reward->sn,
            ];
        } catch (\Exception $e) {
            $code = $e->getCode();
            if (11 === $code) {
                Yii::$app->response->statusCode = 400;
                return [
                    'code' => $code,
                    'message' => '未在本轮红包雨时间段内',
                    'ticket' => null,
                ];
            } elseif (10 === $code) {
                Yii::$app->response->statusCode = 400;
                return [
                    'code' => $code,
                    'message' => '本轮已抽奖',
                    'ticket' => null,
                ];
            }
            return $this->getErrorByCode($code);
        }
    }

    /*
     * 520慈善勋章抽奖活动接口
     * @return array
     * */
    public function actionGetCharityDraw()
    {
        $user = $this->getAuthedUser();
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180520']);
        $promoCharity = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180508']);
        $promoClass = new $promoCharity->promoClass($promoCharity);
        try {
            $this->checkStatus($promo, $user);
            $promoClass->checkDraw($user);
            $joinTime = new \DateTime($promoCharity->endTime);
            $ticket = PromoService::draw($promoCharity, $user, $joinTime);
            $medalCount = $promoClass->getMedalCount($user);
            return [
                'code' => '0',
                'message' => '成功',
                'sn' => $ticket->reward->sn,
                'refAmount' => $ticket->reward->ref_amount,
                'refType' => $ticket->reward->ref_type,
                'medalCount' => $medalCount,
            ];
        } catch (\Exception $e) {
            $code = $e->getCode();
            return $this->getErrorByCode($code);
        }
    }
}
