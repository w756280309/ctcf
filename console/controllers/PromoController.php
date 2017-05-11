<?php

namespace console\controllers;


use common\models\promo\DuoBao;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\console\Controller;

/**
 * 活动定时任务类
 */
class PromoController extends Controller
{
    /**
     * 仅用于 生日送代金券活动
     * 在用户生日当天给用户发代金券
     * 每天8:50执行
     */
    public function actionSendCoupon()
    {
        $promoKey = 'promo_birthday_coupon';
        $promo = RankingPromo::findOne(['key' => $promoKey]);
        if ($promo && class_exists($promo->promoClass)) {
            //活动上线之后需要先判断活动时间，不然当活动结束之后，仍然会查找所有当天生日的用户
            if ($promo->isOnline) {
                $date = date('Y-m-d');
                if ($date < $promo->startTime) {
                    return false;
                }
                if (!empty($promo->endTime) && $date > $promo->endTime) {
                    return false;
                }
            }
            $model = new $promo->promoClass($promo);
            $userList = $model->getAwardUserList();
            $model->sendAwardToUsers($userList);
        }
    }

    /**
     * 仅用于0元夺宝活动 ，补充虚拟抽奖人数
     * 5分钟运行一次，每次最多取3条记录更新
     * 运行时间为2017-05-11 零点开始至2017-05-12 24点结束
     */
    public function actionAddVirtualNum()
    {
        $startTime = '2017-05-10';
        $endTime = '2017-05-12';
        $promo = RankingPromo::findOne(['key' => 'duobao0504']);
        if ($promo) {
            //判断活动是否上线
            if ($promo->isOnline) {
                $date = date('Y-m-d');
                if ($date < $promo->startTime) {
                    return false;
                }
                if (!empty($promo->endTime) && $date > $promo->endTime) {
                    return false;
                }
            }
            $promoAtfr = new DuoBao($promo);

            //计算总的真实参与活动总人数 $r_num_all
            $r_num_all = PromoLotteryTicket::find()
                ->where(['promo_id' => $promo->id])
                ->andWhere(['<>', 'source', 'fake'])
                ->count();
            //总的抽奖数
            $totalTicket = $promoAtfr->totalTicketCount();

            //计算时间范围内新注册参与活动人数 $v_num(应该虚拟的个数)
            $v_num = PromoLotteryTicket::find()
                    ->where(['promo_id' => $promo->id])
                    ->andWhere(['source' => 'new_user'])
                    ->andWhere(['between', 'created_at', strtotime($startTime), strtotime($endTime)])
                    ->count();

            $num = 0;
            $addNum = $r_num_all + $v_num - $totalTicket;
            if ($addNum) {
                $addNum = $addNum >= 3 ? 3 : $addNum;
                $connection = Yii::$app->db;
                for ($i = 1; $i <= $addNum; $i ++) {
                    $transaction = $connection->beginTransaction();
                    try {
                            //更新sequence
                            $sequence = $promoAtfr->joinSequence();
                            if ($sequence > $promoAtfr::TOTAL_JOINER_COUNT) {
                                throw new \Exception('参与人员已满额');
                            }
                            //插入ticket
                            $ticket = new PromoLotteryTicket();
                            $ticket->user_id = '0';
                            $ticket->source = 'fake';
                            $ticket->promo_id = $promo->id;
                            $ticket->joinSequence = $sequence;
                            $ticket->save(false);
                            $num++;
                        $transaction->commit();
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        exit($e->getMessage());
                    }
                    //解决活动页面注册时间显示接近的问题
                    sleep(random_int(10 , 50));
                }
            }
        }
        echo "\n总的抽奖数：";
        echo $totalTicket + $num;

        echo "\n真实参与活动总人数：";
        echo $r_num_all;

        echo "\n应虚拟个数：";
        echo $v_num;

        echo "\n已虚拟个数：";
        echo $totalTicket - $r_num_all + $num;

        echo "\n本次虚拟的个数：";
        echo $num;
        echo "\n";
    }
}