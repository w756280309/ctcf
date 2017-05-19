<?php

namespace common\models\promo;

use common\models\user\User;
use GuzzleHttp\Client;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class DuoBao
{
    const SOURCE_NEW_USER = 'new_user';
    const SOURCE_INVITER = 'inviter';

    const TOTAL_JOINER_COUNT = 1000;

    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 登记参与,发放抽奖机会.
     *
     * 1. 注册时间是活动期间的用户;
     * 2. 注册时间在活动之前，但活动期间有邀请记录的用户;
     */
    public function addTicketForUser(User $user)
    {
        $ticket = null;
        $transcation = Yii::$app->db->beginTransaction();

        try {
            if ($this->promo->isActive($user)
                && !$this->isJoinWith($user)
            ) {
                $source = $this->source($user);

                if (null !== $source) {
                    $sequence = $this->joinSequence();

                    if ($sequence > self::TOTAL_JOINER_COUNT) {
                        throw new \Exception('参与人员已满额');
                    }

                    $duobaoCodeArr = require(__DIR__.'/duobao_code.php');
                    $ticket = PromoLotteryTicket::initNew($user, $this->promo, $source);
                    $ticket->joinSequence = $sequence;
                    $ticket->duobaoCode = $duobaoCodeArr[$sequence-1];
                    $ticket->save(false);

                    $transcation->commit();
                }
            }
        } catch (\Exception $e) {
            $transcation->rollBack();
        }

        return $ticket;
    }

    /**
     * 总参与数.
     *
     * 1. 以当前序列号为总参与数;
     */
    public function totalTicketCount()
    {
        $sequence = PromoSequence::find()->one();
        if (is_null($sequence)) {
            return 0;
        }
        return $sequence->id < self::TOTAL_JOINER_COUNT ? $sequence->id : self::TOTAL_JOINER_COUNT;
    }

    /**
     * 用户是否参与当前活动.
     */
    public function isJoinWith(User $user)
    {
        $ticket = PromoLotteryTicket::findOne([
            'user_id' => $user->id,
            'promo_id' => $this->promo->id,
        ]);

        return null !== $ticket;
    }

    /**
     * 获取用户的类别.
     *
     * 1. 注册时间是活动期间的用户,返回new_user;
     * 2. 注册时间在活动之前，但活动期间有邀请记录的用户,返回inviter;
     * 3. 其他返回null;
     */
    public function source(User $user)
    {
        $source = null;

        try {
            $dateTime = new \DateTime(date('Y-m-d H:i:s', $user->created_at));

            if ($this->promo->inPromoTime($dateTime)) {
                $source = self::SOURCE_NEW_USER;
            }
        } catch (\Exception $e) {
            if (1 === $e->getCode()) {
                $invite = InviteRecord::find()
                    ->where(['user_id' => $user->id])
                    ->andWhere(['>=', 'created_at', strtotime($this->promo->startTime)]);

                if (!empty($this->promo->endTime)) {
                    $invite->andWhere(['<=', 'created_at', strtotime($this->promo->endTime)]);
                }

                if ($invite->count()) {
                    $source = self::SOURCE_INVITER;
                }
            }
        }

        return $source;
    }

    /**
     * 判断手机号是否为浙江省号段手机号.
     */
    public function isZhejiangMobile($mobile)
    {
        try {
            $client = new Client([
                'connect_timeout' => 2,
                'timeout' => 2,
            ]);

            $request = $client->request('GET', 'https://tcc.taobao.com/cc/json/mobile_tel_segment.htm?tel='.$mobile);

            $resp = mb_convert_encoding($request->getBody()->getContents(), 'UTF-8', 'GB18030');
            preg_match("/province:'([^']+)'/u", $resp, $match);

            return mb_substr($match[1], 0, 2, 'UTF-8') === '浙江';
        } catch (\Exception $e) {
            //DO NOTHING
        }

        return true;
    }

    /**
     * 判断活动时间.
     *
     * 1. 活动未开始,返回1;
     * 2. 活动中,返回2;
     * 3. 活动已结束,返回3;
     */
    public function promoTime()
    {
        $code = 2;

        if ($this->totalTicketCount() < 1000) {
            try {
                $this->promo->isActive();
            } catch (\Exception $e) {
                $exceptionCode = $e->getCode();

                if (1 === $exceptionCode) {
                    $code = 1;
                } elseif (2 === $exceptionCode) {
                    $code = 3;
                }
            }
        } else {
            $code = 3;
        }

        return $code;
    }

    public function joinSequence()
    {
        $db = Yii::$app->db;

        $db->createCommand('UPDATE promo_sequence SET id=LAST_INSERT_ID(id+1);')->execute();
        $sequence = $db->createCommand('SELECT LAST_INSERT_ID();')->queryColumn();

        return $sequence[0];
    }
}
