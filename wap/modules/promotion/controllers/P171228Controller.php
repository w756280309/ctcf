<?php

namespace wap\modules\promotion\controllers;

use common\models\promo\Award;
use common\models\promo\Callout;
use common\models\promo\CalloutResponder;
use common\models\promo\InviteRecord;
use common\models\promo\TicketToken;
use common\models\thirdparty\SocialConnect;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P171228Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     * 落地页-（发起助力页面）
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171228']);
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $callOut = Callout::findByPromoUser($promo, $user)->one();
            if (null !== $callOut) {
                return $this->redirect('calling');
            }
        }

        return $this->render('index');
    }

    /**
     * 处理发起助力Action
     */
    public function actionDoCall()
    {
        //活动状态判断
        $promo = RankingPromo::findOne(['key' => 'promo_171228']);
        if (null === $promo) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        $promoStatus = $this->getPromoStatus($promo);
        if ($promoStatus > 0) {
            return 1 === $promoStatus
                ? $this->getErrorByCode(self::ERROR_CODE_NOT_BEGIN)
                : $this->getErrorByCode(self::ERROR_CODE_ALREADY_END);
        }

        //登录状态判断
        $user = $this->getAuthedUser();
        if (null === $user) {
            return $this->getErrorByCode(self::ERROR_CODE_NOT_LOGIN);
        }

        //投资状态判断，若未投资，则提示投资用户才可添加
        $isInvested = false;
        $info = $user->info;
        if (null !== $info) {
            $isInvested = $info->getTotalInvestMoney() > 0;
        }
        if (!$isInvested) {
            return $this->getErrorByCode(self::ERROR_CODE_NEVER_GOT_TICKET);
        }

        //已经发起召集
        $callOut = Callout::findByPromoUser($promo, $user)->one();
        if (null !== $callOut) {
            return $this->getErrorByCode(self::ERROR_CODE_TODAY_NO_TICKET);
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $token = 'callOut-' . $promo->id . '-' . $user->id;
            $endTime = new \DateTime($promo->endTime);
            TicketToken::initNew($token)->save(false);
            $callerOpenId = Yii::$app->session->get('resourceOwnerId');
            Callout::initNew($user, $endTime, $promo->id, $callerOpenId)->save(false);
            $transaction->commit();

            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '发起召集成功',
                'ticket' => $user->usercode,
            ];
        } catch (\yii\db\IntegrityException $ex) {
            $transaction->rollBack();
            if (23000 === $ex->getCode()) {
                return $this->getErrorByCode(self::ERROR_CODE_TODAY_NO_TICKET);
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
    }

    /**
     * 助力中页面
     */
    public function actionCalling()
    {
        //判断是否登录
        $user = $this->getAuthedUser();
        if (null === $user) {
            return $this->redirect('/site/login');
        }

        //还未发起召集
        $promo = RankingPromo::findOne(['key' => 'promo_171228']);
        $callout = Callout::findByPromoUser($promo, $user)->one();
        if (null === $callout) {
            return $this->redirect('index');
        }

        //获取投资金额
        $investMoney = 0;
        $urls = [];
        //获得当前用户的获奖金额及用户ID
        if ($callout->responderCount > 0) {
            $investMoney = (float) Award::findByPromoUser($promo, $user)
                ->sum('amount');

            //根据用户ID查找所有邀请的参加活动好友完成首投的微信头像图片，最多6个
            $uids = Award::findByPromoUser($promo, $user)
                ->select('ticket_id')
                ->column();

            //根据用户ID的集合获取微信头像
            $urls = $this->getHeadImgUrls($uids, FE_BASE_URI.'wap/campaigns/active20171221/images/default-avatar.png');
        }

        return $this->render('calling', [
            'investMoney' => $investMoney,
            'urls' => $urls,
            'userCode' => $user->usercode,
        ]);
    }

    private function getHeadImgUrls(Array $uids, $defaultImgUrl)
    {
        $openIds = SocialConnect::find()
            ->select('resourceOwner_id')
            ->where(['in', 'user_id', $uids])
            ->indexBy('user_id')
            ->column();

        $imgUrls = [];
        $wxsInfo = $this->getWxsInfo($openIds);
        if (empty($wxsInfo)) {
            $imgUrls = array_fill(0, count($uids), $defaultImgUrl);
        } else {
            $infoList = $wxsInfo['user_info_list'];
            foreach ($uids as $uid) {
                if (isset($openIds[$uid])) {
                    foreach ($infoList as $info) {
                        if ($openIds[$uid] === $info['openid']) {
                            $imgUrls[] = isset($info['headimgurl']) ? $info['headimgurl'] : $defaultImgUrl;
                        }
                    }
                } else {
                    $imgUrls[] = $defaultImgUrl;
                }
            }
        }

        return $imgUrls;
    }

    /**
     * 分享页面
     */
    public function actionShare()
    {
        //判断参数是否正确
        $inv = Yii::$app->request->get('inv');
        if (empty($inv) || null === ($invUser = User::find()->where(['usercode' => $inv])->one())) {
            throw $this->ex404();
        }

        //判断是否登录
        $user = $this->getAuthedUser();
        if (null !== $user) {
            return $this->redirect('index');
        }

        //获取微信头像
        $wxInfo = $this->getWxInfo($invUser->id);
        if (null === $wxInfo['headImgUrl']) {
            $wxInfo['headImgUrl'] = FE_BASE_URI.'wap/campaigns/active20171221/images/default-avatar.png';
        }

        return $this->render('share', [
            'headImgUrl' => $wxInfo['headImgUrl'],
            'inv' => $inv,
        ]);
    }

    /**
     * 点击助力
     */
    public function actionDo()
    {
        //判断参数是否正确
        $inv = Yii::$app->request->post('inv');
        if (empty($inv) || null === ($invUser = User::find()->where(['usercode' => $inv])->one())) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断是否是微信
        if (!$this->fromWx()) {
            Yii::$app->response->statusCode = 400;
            return [
                'code' => 10,
                'message' => '请在微信中助力',
            ];
        }

        //判断是否登录
        $user = $this->getAuthedUser();
        if (null !== $user) {
            Yii::$app->response->statusCode = 400;
            return [
                'code' => 8,
                'message' => '已登录',
            ];
        }

        //召集者是否发起了召集
        $promo = RankingPromo::findOne(['key' => 'promo_171228']);
        $callOut = Callout::findByPromoUser($promo, $invUser)->one();
        if (null === $callOut) {
            return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
        }

        //判断是否为本人点击助力
        $openId = Yii::$app->session->get('resourceOwnerId');
        if ($callOut->callerOpenId === $openId) {
            Yii::$app->response->statusCode = 400;
            return [
                'code' => 9,
                'message' => '不能助力自己哦！',
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $callOutResponder = CalloutResponder::initNew($openId, $callOut, $promo->id);
            $callOutResponder->save(false);
            Yii::$app->session->set('inviteCode', $inv);
            $transaction->commit();

            return [
                'code' => self::STATUS_SUCCESS,
                'message' => '助力成功',
            ];
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $this->getErrorByCode(self::ERROR_CODE_SYSTEM);
    }
}
