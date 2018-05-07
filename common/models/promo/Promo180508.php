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
}