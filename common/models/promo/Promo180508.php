<?php

namespace common\models\promo;

use Faker\Provider\DateTime;
use Yii;
use common\models\user\User;
use yii\web\Request;

class Promo180508 extends BasePromo
{
    //判断梦想文字是否达到指定的长度并保存
    public function saveTicketContent($ticketId, $content, $type)
    {
        $content = trim($content);
        if (mb_strlen($content, 'UTF-8') < 5) {
            throw new \Exception('字数不足', 10);
        }

        if (mb_strlen($content, 'UTF-8') > 50) {
            throw new \Exception('字数溢出', 11);
        }
        if ($type === 'insert') {
            Yii::$app->db->createCommand("insert into item_message(`ticketId`, `content`) VALUES (:ticketId, :content)", [
                'ticketId' => $ticketId,
                'content' => $content,
            ])->execute();
        } elseif ($type === 'update') {
            Yii::$app->db->createCommand("update item_message set content = :content WHERE ticketId = :ticketId", [
                'content' => $content,
                'ticketId' => $ticketId,
            ])->execute();
        }
    }

    //获取梦想的内容
    public function getContent(User $user)
    {
        $ticket = $this->fetchOneActiveTicket($user, 'free');
        if ($ticket) {
            $message = Yii::$app->db->createCommand("select content from item_message where ticketId = :ticketId", [
                'ticketId' => $ticket->id,
            ])->queryOne();

            return $message['content'];
        } else {
            throw new \Exception('无有效抽奖机会', 4);
        }
    }

    //邀请用户注册成功后给邀请者增加抽奖机会
    public function addTicket(User $user, $ticketSource, Request $request = null)
    {
        if ($this->promo->isActive($user)) {
            switch ($ticketSource) {
                case 'register'://新用户注册
                    $this->addInviteTicketInternal($user, $request, 'invite', new \DateTime('2018-05-20 23:59:59'));//给邀请者送抽奖机会
                    break;
                case 'init'://用户进入抽奖页面
                    $this->addInitTicketInternal($user, $request);//用户进入抽奖页面给抽奖机会
                    break;
            }
        }
    }

    /*
     * 奖池配置
     * 用户第一次抽奖一个奖池，第二次及之后更换奖池
     * 如果库存为0，则将概率添加到5.2现金红包上
     * @return array
     * */
    public function getAwardPool(User $user, $joinTime)
    {
        $firstDraw = [
            '180520_RP0.52' => '0.299',
            '180520_RP0.66' => '0.1',
            '180520_RP0.88' => '0.1',
            '180520_P16' => '0.25',
            '180520_P18' => '0.25',
            '180520_RP5.2' => '0.001',
        ];
        $secondDraw = [
            '180520_RP5.2' => '0.299',
            '180520_RP6.6' => '0.2',
            '180520_RP8.8' => '0.1',
            '180520_P88' => '0.25',
            '180520_P166' => '0.15',
            '180520_RP52' => '0.001',//库存1
        ];
        $count = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['isRewarded' => true])
            ->count();
        if ($count < 1) {
            $pool = $firstDraw;
        } else {
            $pool = $this->reviseStocksRate($secondDraw, '180520_RP5.2');
        }
        return $pool;
    }

    /*
     * 获取剩余抽奖机会(慈善勋章数量)
     *@param $user
     *@return int
     * */
    public function getMedalCount(User $user)
    {
        $count = $this->getActiveTicketCount($user);
        $result = !empty($count) ? $count : 0;
        return $result;
    }

    /*
     * 检测能否抽奖
     * @param $user
     * @return Exception
     * */
    public function checkDraw(User $user)
    {
        $count = $this->getMedalCount($user);
        if ($count < 1) {
            throw new \Exception('无抽奖机会', 4);
        }
    }
}