<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\TicketToken;

class P171111Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    const SOURCE_ORDER = 'appointment';
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
        $time = !empty($time) ? $time : time();
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
    public function actionGetInitialize($time = null)
    {
        //活动状态
        $time = !empty($time) ? $time : time();
        $currentDate = date('Ymd',$time);
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
        if ($currentTime >= '10:00:00'){
            if ($perSecondKillCount[0] >= $repertoryInfo[0]['repertoryCount']) {
                $activeNav = 1;
                $secondKillList[0]['secondKillStatus'] = 2;
            }else {
                $secondKillList[0]['secondKillStatus'] = 0;
            }
        }
        if ($currentTime >= '15:00:00') {
            $activeNav = 1;
            if ($perSecondKillCount[1] >= $repertoryInfo[1]['repertoryCount']) {
                $activeNav = 2;
                $secondKillList[1]['secondKillStatus'] = 2;
            } else {
                $secondKillList[1]['secondKillStatus'] = 0;
            }
        }
        if ($currentTime >= '20:00:00' && $currentTime < '23:59:59') {
            $activeNav = 2;
            if ($perSecondKillCount[2] >= $repertoryInfo[1]['repertoryCount']) {
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
                'rateCoupon' => null,
            ];
        }
        $user = $this->getAuthedUser();
        $appointmentNumber = \Yii::$app->db->createCommand(
            "select count(id) from appliament WHERE userId=" . $user->id)
            ->queryScalar();

        if ($appointmentNumber == 0) {
            $promo = RankingPromo::findOne(['key' => 'promo_171108']);
            $expireTime = new \DateTime($promo->endTime);
            $tranaction = \Yii::$app->db->beginTransaction();
            try {
                $key = $promo->id . '-' . $user->id . '-' . self::SOURCE_ORDER;
                TicketToken::initNew($key)->save(false);
                PromoLotteryTicket::initNew($user, $promo, self::SOURCE_ORDER, $expireTime)->save(false);
                $tranaction->commit();
            } catch (\Exception $ex){
                $tranaction->rollBack();
                throw $ex;
            }
        }
        $record = \Yii::$app->db->createCommand(
            "insert into appliament(`userId`,`appointmentTime`,`appointmentAward`,`appointmentObjectId`) VALUES 
            ($user->id,$appointmentTime,$appointmentAward,$appointmentObjectId)")
            ->execute();
        if ($record) {
            $rateCoupon = $this->getCouponInfo($appointmentAward, $appointmentObjectId);
            $appliamentResult['code'] = 0;
            $appliamentResult['rateCoupon'] = $rateCoupon;
        } else {
            $appliamentResult['code'] = 1;
            $appliamentResult['rateCoupon'] = null;
        }
        return $appliamentResult;
    }
    //活动二立即秒杀接口
    public function actionSecondKill($activeNumber = null, $time = null)
    {
        $returnValue = [
            1 => ['code' => 0, 'message' => '秒杀成功', 'prize' => ['activityNumber'=> $activeNumber]],
            2 => ['code' => 1, 'message' => '活动未开始', 'prize' => ['activityNumber'=> $activeNumber]],
            3 => ['code' => 2, 'message' => '秒杀失败',  'prize' =>['activityNumber'=> $activeNumber]],
            4 => ['code' => 3, 'message' => '已秒杀完', 'prize' =>['activityNumber'=> $activeNumber]],
            5 => ['code' => 5, 'message' => '不能再次秒杀', 'prize' => ['activityNumber'=> $activeNumber]],
            6 => ['code' => 6, 'message' => '尚未登录！', 'prize' => ['activityNumber'=> $activeNumber]],
            7 => ['code' => 7, 'message' => '奖品编号错误', 'prize' => ['activityNumber'=> $activeNumber]]
        ];
        $time = !empty($time) ? $time : time();
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
        $secondKillCount = $db->createCommand(
            "select count(id) from second_kill where term=".$activeNumber)
            ->queryScalar();
        $repertoryInfo = $this->getRepertoryInfo($activeNumber);
        if ($secondKillCount >= $repertoryInfo['repertoryCount']) {
            return $returnValue[4];
        } else {
            try {
                $record = $db->createCommand(
                    "insert into second_kill(`userId`,`createTime`,`term`) VALUES ($user->id,$time,$activeNumber)")
                    ->execute();
            } catch (\Exception $e) {
                if (23000 === $e->getCode()) {
                    return $returnValue[5];
                }
                throw $e;
            }

            if (!$record) {
                return $returnValue[3];
            }
            return $returnValue[1];
        }
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
                break;
            case '2017110615':
                $repertoryInfo['repertoryCount'] = 39;
                $repertoryInfo['repertoryName'] = '海天调味礼盒';
                break;
            case '2017110620':
                $repertoryInfo['repertoryCount'] = 30;
                $repertoryInfo['repertoryName'] = 'Aquafresh三色牙膏';
                break;
            case '2017110710':
                $repertoryInfo['repertoryCount'] = 3;
                $repertoryInfo['repertoryName'] = '空气加湿器';
                break;
            case '2017110715':
                $repertoryInfo['repertoryCount'] = 11;
                $repertoryInfo['repertoryName'] = '电子血压计';
                break;
            case '2017110720':
                $repertoryInfo['repertoryCount'] = 5;
                $repertoryInfo['repertoryName'] = '美的养生壶';
                break;
            case '2017110810':
                $repertoryInfo['repertoryCount'] = 10;
                $repertoryInfo['repertoryName'] = '特质纸巾';
                break;
            case '2017110815':
                $repertoryInfo['repertoryCount'] = 6;
                $repertoryInfo['repertoryName'] = '美的电水壶';
                break;
            case '2017110820':
                $repertoryInfo['repertoryCount'] = 2;
                $repertoryInfo['repertoryName'] = '小米电饭煲';
                break;
        }
        return $repertoryInfo;
    }
}
