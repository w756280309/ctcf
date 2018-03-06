<?php

namespace console\controllers;


use common\lib\user\UserStats;
use common\models\promo\DuoBao;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\user\User;
use common\utils\SecurityUtils;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

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
        Yii::info('[command][promo/send-coupon] 生日当天送代金券　正常开始', 'command');
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
            Yii::info('[command][promo/send-coupon] 生日当天送代金券　准备发代金券', 'command');
            $model = new $promo->promoClass($promo);
            $userList = $model->getAwardUserList();
            Yii::info('[command][promo/send-coupon] 生日当天送代金券　找到'.count($userList).'个需要发生日券的用户', 'command');
            $model->sendAwardToUsers($userList);
        }
        Yii::info('[command][promo/send-coupon] 生日当天送代金券　正常结束' . "\n");
    }

    /**
     * 仅用于0元夺宝活动 ，补充虚拟抽奖人数
     * 5分钟运行一次，每次最多取3条记录更新
     * 运行时间为2017-05-11 零点开始至2017-05-12 24点结束
     */
    public function actionAddVirtualNum()
    {
        $startTime = '2017-05-11';
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

    /**
     * 仅用于0元夺宝活动增加虚拟参与人数
     * 运行时间 5.13-5.14 8点-23点
     * 5分钟运行一次 随机增加1-6条记录
     * 每条记录随机间隔10-50s
     * $totalTicketNum 输入总虚拟抽奖人数
     * 赵瑞璞要求虚拟到1605
     * ----------------------------
     * 方法修改
     * 输入数据验证int
     * 运行时间今日15号4-9点
     * 每条记录随机间隔10-40s
     * $totalTicketNum 输入总虚拟抽奖人数
     * 不做额外校验
     */
    public function actionAddFakeNum ($totalTicketNum) {
        if (!is_numeric($totalTicketNum)) {
            echo "请输入一个数字";
            echo "\n";
            return false;
        }
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
            //总的抽奖数
            $totalTicket = $promoAtfr->totalTicketCount();
            if ($totalTicket >= $totalTicketNum) {
                echo "已经达到设定抽奖人数上限";
                echo "\n";
                return false;
            }
            $connection = Yii::$app->db;
            $addNum = random_int(1 , 8);
            $num = $sequence = 0;
            for ($i = 1; $i <= $addNum; $i ++) {
                $transaction = $connection->beginTransaction();
                try {
                    //更新sequence
                    $sequence = $promoAtfr->joinSequence();
                    if ($sequence > $promoAtfr::TOTAL_JOINER_COUNT || $sequence > $totalTicketNum) {
                        throw new \Exception('参与人员已满额或者虚拟抽奖数达到上限');
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
                sleep(random_int(10 , 30));
            }
            echo "\n本次虚拟的个数：";
            echo $num;
        }
        echo "\n";
    }

    /**
     * 2017年11月11日活动二在11月6日-8日预约加息券
     * 2017年11月9日0点发送加息券
     */

    public function actionSendDoubleElevenCoupon()
    {
        $date = date('Y-m-d');
        if ($date > '2017-11-11') {
            \Yii::info("[command][promo/send-double-eleven-coupon]活动已结束", 'command');
            return false;
        }
        $promoKey = 'promo_171108';
        $promo = RankingPromo::findOne(['key' => $promoKey]);
        if ($promo && class_exists($promo->promoClass)) {
//          发加息券之前先判断当前时间是不是在活动二结束以后
            if ($promo->isOnline) {
                if (!empty($promo->endTime) && $date < $promo->endTime) {
                    \Yii::info("[command][promo/send-double-eleven-coupon]活动未开始,2017年11月9日零点派发加息券", 'command');
                    return false;
                }
            }
            Yii::info('[command][promo/send-double-eleven-coupon]' . $date . '零点正常开始,准备发加息券', 'command');
            $model = new $promo->promoClass($promo);
            $userList = $model->getAwardUserList();
            Yii::info('[command][promo/send-double-eleven-coupon] 活动期间，　共有'.count($userList).'个用户预约了加息券', 'command');
            $model->sendAwardToUsers($userList);
        }
    }

    /**
     * 注册送演唱会门票活动发奖 -- 仅用一次
     *
     * @param integer $index          上证指数
     * @param integer $limitCount     发奖人数
     * @param bool    $allowPerformed 是否执行发奖操作（插入award表）
     *
     * @throws \Exception
     */
    public function actionAward180119($index, $limitCount, $allowPerformed = false)
    {
        $promo = RankingPromo::findOne(['key' => 'promo_180119']);
        if (null === $promo) {
            throw new \Exception('活动不存在');
        }
        $reward = Reward::fetchOneBySn('180119_G880');
        if (null === $reward) {
            throw new \Exception('活动奖品不存在');
        }

        //得到获奖的用户信息
        $promoClass = new $promo->promoClass($promo);
        $userInfo = $promoClass->getUserInfoByCompositeIndex($index, $limitCount);

        //输出中奖用户信息数组
        $this->stdout('接下来为待执行的获奖用户信息数组'.PHP_EOL);
        print_r($userInfo);

        if ($allowPerformed) {
            $this->stdout('-----开始发奖-----'.PHP_EOL);
            $num = 0;
            $userIds = ArrayHelper::getColumn($userInfo, 'id');
            $users = User::find()
                ->where(['in', 'id', $userIds])
                ->all();
            foreach ($users as $user) {
                PromoService::award($user, $reward, $promo);
                $num++;
            }
            $this->stdout('共插入'.$num.'条数据');
        }
    }

    /**
     * 导出某个活动未抽奖的用户信息
     * 脚本命令：
     * php yii promo/export-by-id 54
     */
    public function actionExportById($promoId)
    {
        $sql = 'select u.real_name,u.safeMobile,count(*) total from promo_lottery_ticket p inner join user u on p.user_id=u.id where p.promo_id = :promoId and p.isDrawn = false group by p.user_id';
        $users = Yii::$app->db->createCommand($sql, [
            'promoId' => $promoId
        ])->queryAll();
        if (empty($users)) {
            $this->stdout('无用户信息待导出');
            return self::EXIT_CODE_ERROR;
        }

        //手机号解密
        foreach ($users as $k => $user) {
            $users[$k]['safeMobile'] = SecurityUtils::decrypt($users[$k]['safeMobile']);
        }

        //导出到console/runtime目录下
        $title = ['姓名', '手机号', '剩余机会'];
        array_unshift($users, $title);
        $file = Yii::getAlias('@app/runtime/lottery_ticket_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($users);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }
}
