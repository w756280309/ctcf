<?php

namespace common\models\promo;


use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use yii\web\Request;

/**
 * 抽奖拉新活动：幸运双十二，疯抢Ipad
 */
class Promo1212
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    //给指定用户添加抽奖机会
    public function addTicket(User $user, $ticketSource, Request $request = null)
    {
        switch ($ticketSource) {
            case 'register'://新用户注册
                $this->addInviteTicketInternal($user, $request);//给邀请者送抽奖机会
                break;
            case 'init'://用户进入抽奖页面
                $this->addInitTicketInternal($user, $request);//用户进入抽奖页面给抽奖机会
                break;
        }
    }

    private function addInitTicketInternal(User $user, Request $request = null)
    {
        //获取用户初始化的抽奖机会
        $ticketCount = PromoLotteryTicket::find()->where(['user_id' => $user->id, 'source' => 'init', 'promo_id' => $this->promo->id])->count();
        if ($ticketCount === 0) {
            $this->addTicketInternal($user->id, 'init', $this->promo->id, empty($request) ? '' : $request->getUserIP());
        }
    }

    /**
     * 给邀请用户赠送抽奖机会
     * @param User $newUser 新注册用户
     * @param Request|null $request
     */
    private function addInviteTicketInternal(User $newUser, Request $request = null)
    {
        //获取邀请当前用户的人
        $inviteRecord = InviteRecord::find()->where(['invitee_id' => $newUser->id])->andWhere(['>=', 'created_at', $this->promo->startAt])->one();
        if (!empty($inviteRecord)) {
            $inviterId = $inviteRecord->user_id;
            //获取邀请者在活动期间邀请人数
            $inviteCount = InviteRecord::find()->where(['user_id' => $inviterId])->andWhere(['>=', 'created_at', $this->promo->startAt])->count();
            //获取当前用户因为邀请被赠送的抽奖机会
            $ticketCount = PromoLotteryTicket::find()->where(['user_id' => $inviterId, 'source' => 'invite', 'promo_id' => $this->promo->id])->count();
            //用户第一次邀请，给一次抽奖机会
            if ($inviteCount === 1 && $ticketCount === 0) {
                $this->addTicketInternal($inviterId, 'invite', $this->promo->id, empty($request) ? '' : $request->getUserIP());
            } elseif ($inviteCount > 1) {
                $deserveCount = $inviteCount * 2 - 1;//应该获取的机会 echo $deserveCount;die;
                $lastCount = $deserveCount - $ticketCount;//需要添加机会
                if ($lastCount > 0) {
                    for ($i = 1; $i <= $lastCount; $i++) {
                        $this->addTicketInternal($inviterId, 'invite', $this->promo->id, empty($request) ? '' : $request->getUserIP());
                    }
                }
            }
        }
    }

    private function addTicketInternal($userId, $source, $promoId, $ip = null)
    {
        $ticket = new PromoLotteryTicket([
            'user_id' => $userId,
            'source' => $source,
            'promo_id' => $promoId,
            'ip' => $ip,
        ]);
        $ticket->save(false);
    }
}