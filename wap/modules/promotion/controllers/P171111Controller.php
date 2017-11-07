<?php

namespace wap\modules\promotion\controllers;

use common\models\mall\PointRecord;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use yii\helpers\ArrayHelper;

class P171111Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    const SOURCE_APPOINTMENT = 'appointment';
    /**
     * 活动总览页
     */
    public function actionTotal()
    {
        $promos = RankingPromo::find()
            ->where(['in', 'key', $this->getAllPromoKey()])
            ->orderBy(['endTime' => SORT_ASC])
            ->all();
        //活动配置不足，直接抛404
        if (count($promos) < 3) {
            throw $this->ex404();
        }
        $promoArr = [
            'promoStatus1' => 0,
            'promoStatus2' => 0,
            'promoStatus3' => 0,
            'activeTicketCount' => 0,
        ];

        //初始化3个分活动的活动状态
        foreach ($promos as $k => $promo) {
            $statusKey = 'promoStatus'.($k+1);
            $promoArr[$statusKey] = $this->getPromoStatus($promo);
        }

        //获取活动的有效抽奖机会
        $promoClass = new $promo->promoClass($promo);
        if (null !== ($user = $this->getAuthedUser())) {
            $promoArr['activeTicketCount'] = $promoClass->getActiveTicketCount($user);
        }

        return $this->render('total', $promoArr);
    }

    private function getAllPromoKey()
    {
        return [
            'promo_171103',
            'promo_171108',
            'promo_171111',
        ];
    }

    /**
     * 分活动1
     */
    public function actionFirst()
    {
        $data = [
            'inviteTask' => 0,
            'investTask' => 0,
        ];

        $user = $this->getAuthedUser();
        if (null !== $user) {
            $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171103']);
            $promoClass = new $promo->promoClass($promo);
            $data = $promoClass->getPromoTaskStatus($user);
        }

        return $this->render('first', $data);
    }
    // 双十一活动二：首次加载页面
    public function actionSecond($time = null)
    {
        $promoStatus = 1;
        $time = time();
        $current = date('Y-m-d H:i:s',$time);
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171108']);
        $startTime = $promo->startTime;
        $endTime = $promo->endTime;
        if ($current > $startTime && $current < $endTime) {
            $promoStatus = 0;
        } else if ($current >= $endTime) {
            $promoStatus = 2;
        }

        //写入layout文件
        $view = \Yii::$app->view;
        $view->params['promoStatus'] = $promoStatus;
        return $this->render('second',['time' => $time]);
    }
    //活动二页面初始化接口
    public function actionGetInitialize()
    {
        //活动状态
        $time = time();
        $currentDate = date('Ymd',$time);
        $newDate = $currentDate;
        $currentDate = !($currentDate < '20171106') ? $currentDate : '20171106';
        $currentDate = !($currentDate > '20171108') ? $currentDate : '20171108';
        $currentTime = date('H:i:s',$time);
        $isAppointmented = 0;                       //默判断是否预约过，0表示未预约过
        $json = [];                                 //预约过后返回的加息券信息
        $rateCoupon = [];
        $secondKillRecord = 0;                       //默认没有秒杀记录
        $isVested = 0;                              //投资状态信息，0表示未投资过
        $isLogin = 0;                               //登录状态,1为已登录
        $activeNav = 0;                             //第一个导航条高亮
        $activeTime = ['10', '15', '20'];
        $secondKillList = [
            ['secondKillStatus' => 1],               //秒杀活动状态，1表示未开始,0为进行中，2为已秒杀完
            ['secondKillStatus' => 1] ,
            ['secondKillStatus' => 1]
        ];

        $perSecondKillCount = [0,0,0];              //初始化每天的三个物品数量，都为0
        $db = \Yii::$app->db;
        //立即秒杀接口
        for($i = 0; $i <= 2; $i++) {
            $secondKillList[$i]['activityNumber'] = $currentDate . $activeTime[$i];
            $perSecondKillCount[$i] = $db->createCommand(
                "select count(id) from second_kill WHERE term=" . $secondKillList[$i]['activityNumber'])
                ->queryScalar();
            $repertoryInfo[$i] = $this->getRepertoryInfo($currentDate . $activeTime[$i]);
        }
        $isActiveDate = in_array($newDate,['20171106','20171107','20171108']);
        if ($currentTime >= '10:00:00' && $isActiveDate){
            if ($perSecondKillCount[0] >= $repertoryInfo[0]['repertoryCount']) {
                $activeNav = 1;
                $secondKillList[0]['secondKillStatus'] = 2;
            }else {
                $secondKillList[0]['secondKillStatus'] = 0;
            }
        }
        if ($currentTime >= '15:00:00' && $isActiveDate) {
            $activeNav = 1;
            if ($perSecondKillCount[1] >= $repertoryInfo[1]['repertoryCount']) {
                $activeNav = 2;
                $secondKillList[1]['secondKillStatus'] = 2;
            } else {
                $secondKillList[1]['secondKillStatus'] = 0;
            }
        }
        if ($currentTime >= '20:00:00' && $isActiveDate) {
            $activeNav = 2;
            if ($perSecondKillCount[2] >= $repertoryInfo[2]['repertoryCount']) {
                $secondKillList[2]['secondKillStatus'] = 2;
            } else {
                $secondKillList[2]['secondKillStatus'] = 0;
            }
        }
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $isLogin = 1;
            //预约
            $appointmentCount = $db->createCommand(
                "select count(id) from appliament WHERE userId = $user->id")
                ->queryScalar();
            if ($appointmentCount > 0) {
                $isAppointmented = 1;
                $appointmentInfo = $db->createCommand(
                    "select appointmentAward, appointmentObjectId from appliament 
                    WHERE userId = $user->id ORDER BY id DESC ")
                    ->queryOne();
                $appointmentAward = $appointmentInfo['appointmentAward'];             //预约金额
                $appointmentObjectId = $appointmentInfo['appointmentObjectId'];       //预约类型
                //利率
                $rateCoupon = $this->getCouponInfo($appointmentAward, $appointmentObjectId);
            }
            //秒杀记录是否显示
            $secondKillRecordCount = $db->createCommand(
                "select count(id) from second_kill WHERE userId = $user->id"
            )->queryScalar();
            if ($secondKillRecordCount > 0) {
                $secondKillRecord = 1;
            }
            //投资接口
            $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171108']);
            $promoClass = new $promo->promoClass($promo);
            $data = $promoClass->getPromoTaskStatus($user);
            $isVested = $data['investTask'];
        }
        $json['isLogin'] = $isLogin;
        $json['isAppointmented'] = $isAppointmented;
        $json['rateCoupon'] = $rateCoupon;
        $json['secondKillRecord'] = $secondKillRecord;
        $json['appointmentTime'] = $time;
        $json['activeNav'] = $activeNav;
        $json['secondKillList'] = $secondKillList;
        $json['isVested'] = $isVested;
        $json['isLogin'] =$isLogin;
        return $json;
    }
    //活动二预约接口
    public function actionAppointment($appointmentAward = null, $appointmentObjectId = null)
    {
        $appointmentTime = time();
        $appliamentResult = [];
        if(\Yii::$app->user->isGuest) {
            return [
                'code' => 2,
                'message' => '没有登录',
                'rateCoupon' => null,
            ];
        }
        if (!preg_match("/^\d*$/",$appointmentAward)) {
            return [
                'code' => 3,
                'message' => '预约金额必须是数字',
                'rateCoupon' => null,
            ];
        }
        if($appointmentAward <= 0 || $appointmentAward > 9999) {
            return [
                'code' => 4,
                'message' => '预约金额在0-9999之间',
                'rateCoupon' => null,
            ];
        }
        $user = $this->getAuthedUser();
        $appointmentNumber = \Yii::$app->db->createCommand(
            "select count(id) from appliament WHERE userId=" . $user->id)
            ->queryScalar();

        if ($appointmentNumber == 0) {
            $promo = RankingPromo::findOne(['key' => 'promo_171108']);
            $promoClass = new $promo->promoClass($promo);
            $promoClass->addUserTicket($user, self::SOURCE_APPOINTMENT);
        }
        $record = \Yii::$app->db->createCommand(
            "insert into appliament(`userId`,`appointmentTime`,`appointmentAward`,`appointmentObjectId`) VALUES 
            ($user->id,$appointmentTime,$appointmentAward,$appointmentObjectId)")
            ->execute();
        if ($record) {
            $rateCoupon = $this->getCouponInfo($appointmentAward, $appointmentObjectId);
            $appliamentResult['code'] = 0;
            $appliamentResult['message'] = '预约成功';
            $appliamentResult['rateCoupon'] = $rateCoupon;
        } else {
            $appliamentResult['code'] = 1;
            $appliamentResult['message'] = '预约失败';
            $appliamentResult['rateCoupon'] = null;
        }
        return $appliamentResult;
    }
    //活动二立即秒杀接口
    public function actionSecondKill($activeNumber = null)
    {
        $returnValue = [
            1 => ['code' => 0, 'message' => '秒杀成功', 'prize' => ['activityNumber'=> $activeNumber]],
            2 => ['code' => 1, 'message' => '活动未开始', 'prize' => ['activityNumber'=> $activeNumber]],
            3 => ['code' => 2, 'message' => '秒杀失败',  'prize' =>['activityNumber'=> $activeNumber]],
            4 => ['code' => 3, 'message' => '已秒杀完', 'prize' =>['activityNumber'=> $activeNumber]],
            5 => ['code' => 5, 'message' => '不能再次秒杀', 'prize' => ['activityNumber'=> $activeNumber]],
            6 => ['code' => 6, 'message' => '尚未登录！', 'prize' => ['activityNumber'=> $activeNumber]],
            7 => ['code' => 7, 'message' => '奖品编号错误', 'prize' => ['activityNumber'=> $activeNumber]],
            8 => ['code' => 8, 'message' => '积分不足', 'prize' => ['activityNumber'=> $activeNumber]]
        ];
        $time = time();
        $redis = \Yii::$app->redis;
        $current = date('YmdH',$time);
        $db = \Yii::$app->db;
        //判断是否是奖池中的商品
        $activityNumberDate = substr($activeNumber, 0, 8);
        $activityNumbers = $this->getAwardKey($activityNumberDate);
        if(!in_array($activeNumber, $activityNumbers)){
            return $returnValue[7];
        }
        //判断是否是登录状态
        if(\Yii::$app->user->isGuest){
            return $returnValue[6];
        }
        $user = $this->getAuthedUser();
        if ($current < $activeNumber) {
            return $returnValue[2];
        }
        $repertoryInfo = $this->getRepertoryInfo($activeNumber);
        $secondKillCount = $redis->LLEN($activeNumber);
        if ($secondKillCount >= $repertoryInfo['repertoryCount']) {
            return $returnValue[4];
        }

        $tranaction = \Yii::$app->db->beginTransaction();
        try {
            $db->createCommand(
                "insert into second_kill(`userId`,`createTime`,`term`) VALUES ($user->id,$time,$activeNumber)")
                ->execute();
            $points = $repertoryInfo['repertoryPoints'];
            PointRecord::subtractUserPoints($user, $points);
            $tranaction->commit();
        } catch (\Exception $e) {
            $tranaction->rollBack();
            if (23000 === $e->getCode()) {
                return $returnValue[5];
            }
            if(8 === $e->getCode()) {
                return $returnValue[8];
            }
            if(9 === $e->getCode()) {
                return $returnValue[3];
            }
            throw $e;
        }

        $redis->LPUSH($activeNumber,$user->id);
        return $returnValue[1];
    }
    //活动二秒杀记录接口
    public function actionSecondKillRecord()
    {
        $data = [
            'code' =>1,
            'message' => '您还没有登录',
            'ticket' => null
        ];
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $data['code'] = 0;
            $secondKillRecord = \Yii::$app->db->createCommand(
                "select term as activityNumber,createTime from second_kill WHERE userId=" . $user->id)
                ->queryAll();
            foreach ($secondKillRecord as $key => $value) {
                $repertoryInfo = $this->getRepertoryInfo($value['activityNumber']);
                $secondKillRecord[$key]['name'] = $repertoryInfo['repertoryName'];
            }
            $data['message'] = '登录成功';
            $data['ticket'] = $secondKillRecord;
        }
        return $data;
    }
    //根据预约金额和预约类型获取加息券信息（利率,最低投资金额,天数,预约类型名称）
    private function getCouponInfo($appointmentAward, $appointmentObjectId)
    {
        $lowestInvestMoney = 1;                         //最低投资金额
        $couponRate = 1;                               //利率
        $couponLength = 10;                            //天数
        $rateCoupon = array();
        if ($appointmentObjectId == 1) {
            $couponLength = 7;
        }
        if($appointmentAward >= 1 && $appointmentAward < 5 && $appointmentObjectId == 0){
            $couponRate = 1;
            $lowestInvestMoney = 1;
        } else if ($appointmentAward >= 5 && $appointmentAward < 20 && $appointmentObjectId == 0) {
            $couponRate = 1.5;
            $lowestInvestMoney = 5;
        } else if ($appointmentAward >= 20 && $appointmentAward < 50 && $appointmentObjectId == 0) {
            $couponRate = 2;
            $lowestInvestMoney = 20;
        } else if ($appointmentAward >= 50 && $appointmentAward < 100 && $appointmentObjectId == 0) {
            $couponRate = 2.5;
            $lowestInvestMoney = 50;
        } else if ($appointmentAward >= 100 && $appointmentObjectId == 0) {
            $couponRate = 3;
            $lowestInvestMoney =100;
        } else if ($appointmentAward >= 1 && $appointmentAward < 5 && $appointmentObjectId == 1) {
            $couponRate = 0.5;
            $lowestInvestMoney = 1;
        } else if ($appointmentAward >= 5 && $appointmentAward < 20 && $appointmentObjectId == 1) {
            $couponRate = 0.6;
            $lowestInvestMoney = 5;
        } else if ($appointmentAward >= 20 && $appointmentAward < 50 && $appointmentObjectId == 1) {
            $couponRate = 0.8;
            $lowestInvestMoney = 20;
        } else if ($appointmentAward >= 50 && $appointmentAward < 100 && $appointmentObjectId == 1) {
            $couponRate = 1.0;
            $lowestInvestMoney = 50;
        } else if ($appointmentAward >= 100 && $appointmentObjectId == 1) {
            $couponRate = 1.2;
            $lowestInvestMoney = 100;
        }
        $rateCoupon['lowestInvestMoney'] = $lowestInvestMoney;
        $rateCoupon['couponRate'] = $couponRate;
        $rateCoupon['couponLength'] = $couponLength;
        $rateCoupon['appointmentObjectId'] = $appointmentObjectId;
        return $rateCoupon;
    }
    //根据活动当天日期获取活动当天3个物品的编号，用于判断前端传过来的编号是否在此编号列表中
    private function getAwardKey($activeNumberDate)
    {
        $activityNumbers = [];
        if ($activeNumberDate === '20171106') {
            $activityNumbers = ['2017110610', '2017110615', '2017110620'];
        } elseif ($activeNumberDate === '20171107') {
            $activityNumbers = ['2017110710', '2017110715', '2017110720'];
        } elseif ($activeNumberDate === '20171108') {
            $activityNumbers = ['2017110810', '2017110815', '2017110820'];
        }
        return $activityNumbers;
    }
//根据物品编号获取秒杀物品的库存量和物品名称
    private function getRepertoryInfo($activityNumber)
    {
        $repertoryInfo = [
            'repertoryCount' => 0,
            'repertoryName' => null
        ];
        switch ($activityNumber) {
            case '2017110610':
                $repertoryInfo['repertoryCount'] = 3;
                $repertoryInfo['repertoryName'] = '50元人本超市卡';
                $repertoryInfo['repertoryPoints'] = 1;
                break;
            case '2017110615':
                $repertoryInfo['repertoryCount'] = 39;
                $repertoryInfo['repertoryName'] = '海天调味礼盒';
                $repertoryInfo['repertoryPoints'] = 1666;
                break;
            case '2017110620':
                $repertoryInfo['repertoryCount'] = 30;
                $repertoryInfo['repertoryName'] = 'Aquafresh三色牙膏';
                $repertoryInfo['repertoryPoints'] = 299;
                break;
            case '2017110710':
                $repertoryInfo['repertoryCount'] = 3;
                $repertoryInfo['repertoryName'] = '空气加湿器';
                $repertoryInfo['repertoryPoints'] = 1;
                break;
            case '2017110715':
                $repertoryInfo['repertoryCount'] = 11;
                $repertoryInfo['repertoryName'] = '电子血压计';
                $repertoryInfo['repertoryPoints'] = 3626;
                break;
            case '2017110720':
                $repertoryInfo['repertoryCount'] = 5;
                $repertoryInfo['repertoryName'] = '美的养生壶';
                $repertoryInfo['repertoryPoints'] = 3266;
                break;
            case '2017110810':
                $repertoryInfo['repertoryCount'] = 10;
                $repertoryInfo['repertoryName'] = '特质纸巾';
                $repertoryInfo['repertoryPoints'] = 1;
                break;
            case '2017110815':
                $repertoryInfo['repertoryCount'] = 6;
                $repertoryInfo['repertoryName'] = '美的电水壶';
                $repertoryInfo['repertoryPoints'] = 1680;
                break;
            case '2017110820':
                $repertoryInfo['repertoryCount'] = 2;
                $repertoryInfo['repertoryName'] = '小米电饭煲';
                $repertoryInfo['repertoryPoints'] = 6660;
                break;
        }
        return $repertoryInfo;
    }

    /**
     * 活动三
     */
    public function actionThird()
    {
        //判断是否开过宝箱
        $drawBoxStatus = 'false';
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_million_1111']);
        $promo11 = $this->findOr404(RankingPromo::class, ['key' => 'promo_171111']);
        $promoClass = new $promo->promoClass($promo);
        $promo11Class = new $promo11->promoClass($promo11);
        $activeTicketCount = 0; //双11活动剩余有效喜卡个数
        $totalMoney = 0; //双11活动期间累计年化
        $requirePopGameBox = 1; //默认未登录状态下弹出游戏弹窗
        if (null !== ($user = $this->getAuthedUser())) {
            try {
                $promoClass->addUserTicket($user, 'free');
            } catch (\Exception $ex) {
                //防止重复插入抽奖机会
            }
            $ticketCount = $promoClass->getActiveTicketCount($user);
            if ($ticketCount > 0) {
                $drawBoxStatus = 'true';
            }
            $activeTicketCount = $promo11Class->getActiveTicketCount($user);
            $startTime = new \DateTime($promo11->startTime);
            $endTime = new \DateTime($promo11->endTime);
            if (null !== $user) {
                $totalMoney = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
            }

            //如果登录状态下不存在“requirePlayGameBox”，则设置此字段，下次登录状态下将不会弹框
            $redis = \Yii::$app->redis;
            if (!$redis->hexists('requirePopGameBox', $user->id)) {
                $redis->hset('requirePopGameBox', $user->id, true);
                $redis->expire('requirePopGameBox', 5 * 24 * 3600);
            } else {
                $requirePopGameBox = 0;
            }
        }
        $this->registerPromoStatusInView($promo11);

        return $this->render('third', [
            'drawBoxStatus' => $drawBoxStatus,
            'activeTicketCount' => $activeTicketCount,
            'totalMoney' => rtrim(rtrim(bcdiv($totalMoney, 10000, 2), '0'), '.'),
            'requirePopGameBox' => $requirePopGameBox,
        ]);
    }

    /**
     * 喜卡兑换列表与百万红包获奖列表合并
     * 列表按照获奖时间倒序排序
     */
    public function actionThirdAwardList()
    {
        $user = $this->getAuthedUser();
        if (null === $user) {
            return [];
        }
        $promo = RankingPromo::findOne(['key' => 'promo_million_1111']);
        $promoCard = RankingPromo::findOne(['key' => 'promo_171111']);
        if (null === $promo || null === $promoCard) {
            return [];
        }

        //获得百万红包的获奖列表
        $promoMillionClass = new $promo->promoClass($promo);
        $millionList = $promoMillionClass->getAwardList($user);
        //获得双11喜卡的获奖列表
        $promoCardClass = new $promo->promoClass($promoCard);
        $cardList = $promoCardClass->getAwardList($user);

        //合并数组
        $awardList = ArrayHelper::merge($cardList, $millionList);
        foreach ($awardList as $k => $award) {
            if (empty($award)) {
                unset($awardList[$k]);
            }
        }

        //按照获奖时间排序
        ArrayHelper::multisort($awardList, 'awardTime', SORT_DESC);

        return $awardList;
    }
}
