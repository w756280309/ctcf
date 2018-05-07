<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\PromoLotteryTicket;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use common\models\user\User;

class P180508Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     *  慈善梦想气球活动初始化页面
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isLoggedIn 判断登录状态，已登录：true,未登录：false
     * freeMedalNum: [0,1]        //放飞梦想气球获得的勋章数量,为0：跳转到初始化页面，为1：跳转到结果页面
     * inviteMedalNum: 5          //邀请好友获得勋章数量
     * ticketId: 50             //当freeMedalNum=1时才有数值，用于修改，查看和保存梦想
     */
    public function actionIndex()
    {
        $code = Yii::$app->request->get('code');
        if (!empty($code)) {
            if (null !== User::find()->where(['usercode' => $code])->one()) {
                Yii::$app->session->set('inviteCode', $code);
            }
        }
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180508']);
        $user = $this->getAuthedUser();
        $ticketId = null;
        $promoClass = new $promo->promoClass($promo);
        $freeMediaNum = 0;
        $allActiveTicket = 0;
        $_csrf = Yii::$app->request->csrfToken;
        if ($user) {
            $activeTicket = $promoClass->fetchOneActiveTicket($user, 'free');
            $allActiveTicket = $promoClass->getActiveTicketCount($user);
            if ($activeTicket !== null) {
                $freeMediaNum = 1;
                $ticketId = $activeTicket->id;
            }
        }

        $data = [
            'promoStatus' => $this->getPromoStatus($promo),
            'isLoggedIn' => null !== $user,
            'freeMedalNum' => $freeMediaNum,
            'inviteMedalNum' => $allActiveTicket - $freeMediaNum,
            'ticketId' => $ticketId,
            'csrf' => $_csrf,
            'isInvite' => !empty($code),
        ];

        $this->renderJsInView($data);

        return $this->render('index');
    }

    /**
     * 放飞梦想气球，保存梦想
     *
     * 提交方式：AJAX get
     * 地址：/promotion/p180505/fly
     * 返回值：
     *      [
     *          'code' => 0,1,2,3,10,23000
     *          'message' => '成功，未开始，已结束，未登录，梦想少于5个字，已得勋章',
     *      ]
     *
     * @return array
     */
    public function actionFly()
    {
        $desc = Yii::$app->request->post('content');
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180508']);
        $user = $this->getAuthedUser();
        $transaction = \Yii::$app->db->beginTransaction();
        $type = 'insert';
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $activeTicket = $promoClass->fetchOneActiveTicket($user, 'free');
            if (!$activeTicket) {
                $key = $promo->id . '-' . $user->id . '-free';
                TicketToken::initNew($key)->save(false);
                //填写的时间用于5月20日慈善抽奖使用
                $ticket = PromoLotteryTicket::initNew($user, $promo, 'free', new \DateTime('2018-05-20 23:59:59'));
                $ticket->save(false);
                $ticketId = $ticket->attributes['id'];
                Yii::info($ticketId, 'user_log');
            } else {
                $type = 'update';
                $ticketId = $activeTicket->id;
            }
            $promoClass->saveTicketContent($ticketId, $desc, $type);
            $transaction->commit();

            return [
                'code' => 0,
                'message' => '成功',
                'freeMedalNum' => 1,
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            $code = $e->getCode();

            return $this->getErrorByCode($code);
        }
    }

    /**
     * 修改/查看梦想
     *
     * 接口地址：/promotion/p180505/revise
     * 输入参数:ticketId，type   // type = [revise,watch]/[修改,查看]
     * 返回字段：
     * {
     *     'code': [0,1,2,3]   //0:进行中，1：活动未开始，2：活动已结束,3:未登录
     *     'message': [‘进行中’,'活动未开始','活动已结束','未登录']
     *     'type': [revise,watch]  //revise为修改梦想， watch为查看梦想
     *      ‘desc’                  //梦想内容
    }
     * }
     */
    public function actionRevise()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180508']);
        $user = $this->getAuthedUser();
        try {
            $this->checkStatus($promo, $user);
            $promoClass = new $promo->promoClass($promo);
            $desc = $promoClass->getContent($user);
            return [
                'code' => 0,
                'message' => '成功',
                'content' => $desc,
            ];
        } catch (\Exception $e) {
            $code = $e->getCode();
            return $this->getErrorByCode($code);
        }
    }
}