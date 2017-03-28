<?php
namespace common\models\promo;

use common\models\affiliation\Affiliator;
use common\models\code\GoodsType;
use common\models\code\VirtualCard;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\SmsService;
use common\utils\SecurityUtils;
use wap\modules\promotion\models\RankingPromo;
use Yii;

/**
 * O2O活动类 - 发送券码短信
 *
 * Class PromoCorp
 * @package common\models\promo
 *
 * //promo的配置项
 * config => [
 *     'image' => '带CDN的绝对地址',
 *     'rules' => [],
 *     'goodsSn' => '', //商品sn
 * ]
 */
class PromoCorp
{
    public $promo;
    public $promoConfig;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        $this->promoConfig = json_decode($promo->config);
    }

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        if ($this->canSend($order)) {
            $config = $this->promoConfig;
            $user = $order->user;
            if (false === ($card = $this->getUsefulCard($user, $config))) {
                return false;
            }

            try {
                $isSend = $this->sendSms($user, $card);
                if ($isSend) {
                    //发送短信成功，更新virtual_card的相关字段
                    $goods = $card->goods;
                    $card->pullTime = date('Y-m-d H:i:s');
                    if (null !== $goods->effectDays) {
                        $card->expiredTime = date('Y-m-d', strtotime('+'. $goods->effectDays .' days')) . ' 23:59:59';
                    }
                    $card->affiliator_id = $goods->affiliator_id;
                    $card->isPull = true;
                    $card->user_id = $user->id;
                    $card->save();
                }
            } catch (\Exception $ex) {
                Yii::info("O2O error：{$ex->getMessage()}，用户ID：{$user->id}，券码：{$card->serial}", 'user_log');
            }
        }
    }

    /**
     * 判断是否可以发券码
     *
     * @param  OnlineOrder $order
     *
     * @return boolean
     */
    public function canSend(OnlineOrder $order)
    {
        try {
            $user = $order->user;
            if (empty($user->promoId) || $user->promoId !== $this->promo->id) {
                return false;
            }
            if ($order->order_money < 1000) {
                return false;
            }
            if (
                $order->status === OnlineOrder::STATUS_SUCCESS
                && $this->promo->isActive($user, $order->order_time)
            ) {
                //活动前没有投资
                $oldOrder = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['<', 'order_time', strtotime($this->promo->startTime)])->one();
                if (!is_null($oldOrder)) {
                    return false;
                }

                //活动期间首次投资
                $query = OnlineOrder::find()->where(['uid' => $user->id, 'status' => OnlineOrder::STATUS_SUCCESS])->andWhere(['>=', 'order_time', strtotime($this->promo->startTime)]);
                if (!empty($this->promo->endTime)) {
                    $query = $query->andWhere(['<=', 'order_time', strtotime($this->promo->endTime)]);
                }
                $firstOrder = $query->orderBy(['order_time' => SORT_ASC])->one();
                if (is_null($firstOrder) || $firstOrder->id !== $order->id) {
                    return false;
                }

                return true;
            }
        } catch (\Exception $ex) {
        }
        return false;
    }

    /**
     * 判断该用户是否已发送券码短信
     *
     * @param  User $user
     *
     * @return boolean
     */
    public function hasAwarded(User $user)
    {
        $config = $this->promoConfig;
        $goodsType = GoodsType::find()
            ->where(['sn' => $config->goodsSn])
            ->one();
        $card = VirtualCard::find()
            ->where(['goodsType_id' => $goodsType->id, 'user_id' => $user->id])
            ->andWhere(['isPull' => true])
            ->andWhere(['isReserved' => false])
            ->one();
        if (null !== $card) {
            return true;
        }
        return false;
    }

    /**
     * 给某个用户发券码短信
     *
     * @param  User        $user
     * @param  VirtualCard $card
     *
     * @return boolean
     */
    public function sendSms(User $user, VirtualCard $card)
    {
        $goods = $card->goods;
        $affiliator = Affiliator::findOne($goods->affiliator_id);
        $templateId = 158404;
        $cardInfo = $card->serial . '，';
        if (null !== $card->secret) {
            $cardInfo .= "密码{$card->secret}，";
        }
        $effectDays = $goods->effectDays;
        if (null !== $effectDays) {
            $cardInfo .= "有效期{$effectDays}天";
        }

        $message = [
            $card->goods->name,
            $cardInfo,
            $affiliator->name,
            $this->promo->title,
            Yii::$app->params['contact_tel'],
        ];

        return SmsService::send(SecurityUtils::decrypt($user->safeMobile), $templateId, $message, $user);
    }

    /**
     * 根据活动配置信息及user返回一个可用的VirtualCard对象
     *
     * @param  User   $user   用户对象
     * @param  array  $confg 活动配置信息
     *
     * @return boolean|VirtualCard
     */
    private function getUsefulCard(User $user, $config)
    {
        $logInfo = '，user_id：' . $user->id . '，promo_id：' . $this->promo->id . '，promo_title：' . $this->promo->title . '，referral_source：' . $user->campaign_source;
        if (empty($user->promoId) || $user->promoId !== $this->promo->id) {
            Yii::info('O2O error：未参与此活动' . $logInfo, 'user_log');
            return false;
        }

        if (!isset($config->goodsSn) || empty($config->goodsSn)) {
            Yii::info('O2O error：请指定活动发送的商品' . $logInfo, 'user_log');
            return false;
        }

        $goodsType = GoodsType::find()
            ->where(['sn' => $config->goodsSn])
            ->one();
        if (null === $goodsType) {
            Yii::info('O2O error：找不到活动发送的商品' . $config->goodsSn . $logInfo, 'user_log');
            return false;
        }

        if (empty($goodsType->affiliator_id) || null === ($affiliator = Affiliator::findOne($goodsType->affiliator_id))) {
            Yii::info('O2O error：找不到提供商品的商家' . $goodsType->affiliator_id . $logInfo, 'user_log');
            return false;
        }

        $card = VirtualCard::find()
            ->where(['goodsType_id' => $goodsType->id])
            ->andWhere(['isPull' => false])
            ->andWhere(['user_id' => null])
            ->andWhere(['isReserved' => false])
            ->one();
        if (null === $card) {
            Yii::info('O2O error：没有可用的券码' . $logInfo, 'user_log');
            return false;
        }

        return $card;
    }
}
