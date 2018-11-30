<?php
/**
 * Created by PhpStorm.
 * User: cz
 * Date: 2018/11/19
 * Time: 17:00
 */

namespace wap\modules\promotion\controllers;

use Model\Order;
use Yii;
use common\models\order\OnlineOrder;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\Award;
use common\models\promo\Reward;
use common\models\mall\PointRecord;
use common\models\user\User20181225DrawRecord;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;
use common\exception\PromoException;
use common\models\transfer\Transfer;
use common\models\code\GoodsType;
use yii\helpers\ArrayHelper;
use common\models\promo\InviteRecord;

class P181225Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    const P20_KEY = '181225_points_20';
    const RP10_KEY = '181225_CTCF_RP10';
    const C20_KEY = '181225_C20';
    const CTCF_XM_KEY = '181225_CTCF_XM';
    const CTCF_DS_KEY = '181225_CTCF_DS';
    const CTCF_IPXS_KEY = '181225_CTCF_IPXS';
    private $beginY = '20181217';
    private $endY = '20181228';
    private $beginTime = '10:00:00';
    private $endTime = '23:59:59';
    private $hashNumKey = 'gifts181225Num';

    private $pool = [
                        self::P20_KEY=>['rate'=>100, 'num'=>100000],
                        self::RP10_KEY=>['rate'=> 50, 'num'=> 30],
                        self::C20_KEY=>['rate'=> 27, 'num'=> 20],
                        self::CTCF_XM_KEY=> ['rate'=> 10, 'num'=> 1],
                        self::CTCF_DS_KEY=> ['rate'=>0, 'num'=>0],
                        self::CTCF_IPXS_KEY=> ['rate'=>0, 'num'=>0],
                    ];

    /**
     * redis连接
     * */
    private function redisConnect()
    {
        $redis = Yii::$app->redis_session;
        return $redis;
    }

    /**
     * 18年圣诞节活动首页
     */
    public function actionIndex()
    {
//        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181225']);
        //echo '活动首页';exit;
        $user = $this->getAuthedUser();
        $isLoggedIn = null !== $user;
        $isGet = 0;
//        $awardlist = [];
        if ($isLoggedIn) {
            $uid = $user->id;
            $awardNums = User20181225DrawRecord::find()
                ->select('sum(draw_nums) as draws')
                ->where(['uid' => $uid])
                ->andwhere(['>=', 'created_at', strtotime($this->beginY)])
                ->andwhere(['<=', 'created_at', strtotime($this->endY . ' 23:59:59')])
                ->asArray()
                ->one();
            if (!empty($awardNums['draws'])) {
                $redis = $this->redisConnect();
                $redis->set('drawNums_' . $uid, $awardNums['draws']);//设置12小时
                $redis->expire('drawNums_' . $uid, 15 * 24 * 60 * 60);
            } else {
                $awardNums = 0;
            }
            //获奖列表
//            $awardlist = $this->getAwardList();
        } else {
            return $this->redirect('/site/login');
        }

        return $this->render('index', [
//            'awardlist'=> $awardlist,
            'awardNums' => $awardNums,
            'isGet' => $isGet,
            'isLoggedIn' => $isLoggedIn,
        ]);
    }

    /**
     * 开礼物
     */
    public function actionOpenGift()
    {
        $user = $this->getAuthedUser();
        if (null === $user) {
            return ['code' => 201, 'message' => '请登录'];
        }

        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181225']);
        $user_id = $user->id;
        //判断是否还有抽奖机会r
        $redis = $this->redisConnect();
        $drawNums = $redis->get('drawNums_' . $user_id);
        if (empty($drawNums)) {
            return ['code' => 202, 'message' => '您的抽奖机会已用完!'];
        }
        //获取投资金额
        $investMoney = $this->getMaxInvestMoney($user_id);
        if (empty($investMoney)) {
            return ['code' => 203, 'message' => '投资金额异常!'];
        }
        try {
            $User20181225DrawModel = new User20181225DrawRecord();
            $User20181225DrawModel->uid = $user->id;
            $User20181225DrawModel->type = User20181225DrawRecord::TYPE_DRAW;
            $User20181225DrawModel->draw_nums = -1;
            if (!$User20181225DrawModel->validate()) {
                return ['code' => 202, 'message' => '参数校验失败' . current($User20181225DrawModel->firstErrors)];
            }
            $ret = $User20181225DrawModel->save(false);
            $reward = Reward::fetchOneBySn($this->sendAward($investMoney, $redis, $promo));
            $sendRes = false;
            if(!empty($reward)){
                switch (strtolower($reward->ref_type)){
                    case 'point':
                        $sendRes = $this->sendScore($reward, $promo);
                        $rewardName = '20账户积分';
                        break;
                    case 'red_packet':
                        $sendRes = $this->sendCash($reward, $promo);
                        $rewardName = '10元现金';
                        break;
                    case 'coupon':
                        $sendRes = $this->sendTicket($reward, $promo);
                        $rewardName = '20代金券';
                        break;
                    case 'xm':
                        $sendRes = $this->sendGoods($reward, $promo);
                        $rewardName = '小米手环3代';
                        break;
                }
            }
            if ($sendRes && Yii::$app->request->isAjax) {
                return [
                    'code' => 200,
                    'message' => '发放成功',
                    'data' => [
                        'rewardName' => $rewardName,
                        'promotype' => $reward->ref_amount,
                    ]
                ];
            }
        } catch (\Exception $ex) {
            return ['code' => 202, 'message' => '发放失败' . $ex->getMessage()];
        }
    }

    /**
     * 判断是否中奖
     * */
    private function checkIsAward($rate){
        return $this->getRandomNum() <= $rate ? 1 : 0;
    }

    public function actionAwards(){
        $user = $this->getAuthedUser();
        $uid = $user->id;
        $awards = Yii::$app->db->createCommand('select * from award where user_id = :id and promo_id = :promo_id',
            [
                'id' => $uid,
                'promo_id' => '67',
            ])
            ->queryAll();
        return $awards;
    }
    /**
     * 开始派奖
    */
    private function sendAward($investMoney, $redis, $promo){
        //查询要求用户
        if($investMoney>=5000 && $redis->hget('gifts181225', self::RP10_KEY)>0){
            if($this->checkIsAward($this->pool[self::RP10_KEY]['rate'])){
                $redis->hincrby('gifts181225', self::RP10_KEY, -1);
                return self::RP10_KEY;
            }
        }
        if(InviteRecord::getFriendsCountByUser($this->getAuthedUser(), $promo->startTime, $promo->endTime) > 0 && $redis->hget('gifts181225', self::RP10_KEY)>0){
            if($this->checkIsAward($this->pool[self::RP10_KEY]['rate'])){
                $redis->hincrby('gifts181225', self::RP10_KEY, -1);
                return self::RP10_KEY;
            }
        }
        if($investMoney>=10000 && $redis->hget('gifts181225', self::C20_KEY)>0){
            if($this->checkIsAward($this->pool[self::C20_KEY]['rate'])){
                $redis->hincrby('gifts181225', self::C20_KEY, -1);
                return self::C20_KEY;
            }
        }
        if($investMoney>=50000 && $redis->hget('gifts181225', self::CTCF_XM_KEY)>0){
            if($this->checkIsAward($this->pool[self::CTCF_XM_KEY]['rate'])){
                $redis->hincrby('gifts181225', self::CTCF_XM_KEY, -1);
                return self::CTCF_XM_KEY;
            }
        }
        if($redis->hget('gifts181225', self::P20_KEY)>0){
            if($this->checkIsAward($this->pool[self::P20_KEY]['rate'])){
                $redis->hincrby('gifts181225', self::P20_KEY, -1);
                return self::P20_KEY;
            }
        }
    }

    /**
     * 获取随机数
    */
    private function getRandomNum($min = 0, $max = 1) {
        return intval($min + mt_rand() / mt_getrandmax() * ($max - $min));
    }

    /**
     * 获取投资金额
     * */
    private function getMaxInvestMoney($uid){
        if(empty($uid)){
            return false;
        }
        $order = OnlineOrder::find()
            ->select('max(order_money) as investMoney')
            ->where(['uid' => $uid])
            ->andwhere(['status' => OnlineOrder::STATUS_SUCCESS])
            ->andwhere(['>=', 'order_time', strtotime($this->beginY)])
            ->andwhere(['<=', 'order_time', strtotime($this->endY . ' 23:59:59')])
            ->asArray()
            ->one();
        return $order['investMoney'];
    }

    /**
     * 分享后添加一次开礼物机会
     */
    public function actionAjax20181225Record()
    {
        $user = $this->getAuthedUser();
        if (empty($user)) {
            return ['code' => 201, 'message' => '请登录'];
        }
        $redis = $this->redisConnect();
        if($redis->hexists('sharewx', $user->id.':'.date('Ymd'))){
            return ['code' => 204, 'message' => '今天已经分享了'];
        }
        try {
            $model = new User20181225DrawRecord();
            $model->uid = $user->id;
            $model->type = User20181225DrawRecord::TYPE_SHARE;
            $model->draw_nums = 1;//+1
            if (!$model->validate()) {
                return ['code' => 202, 'message' => '参数校验失败' . current($model->firstErrors)];
            }
            $ret = $model->save(false);
            if (Yii::$app->request->isAjax && $ret) {
                $redis->hmset('sharewx', $user->id.':'.date('Ymd'), 1);
                $redis->expire('sharewx', 5);
                return ['code' => 200, 'message' => '添加成功'];
            }

        } catch (\Exception $ex) {
            return ['code' => 203, 'message' => '添加失败' . $ex->getMessage()];
        }

    }

    /**
     * 发放加息券，满减券
     */
    private function sendTicket($reward, $promo)
    {
        if(empty($reward)){
            return false;
        }
        $user = $this->getAuthedUser();
        $couponType = CouponType::find()
            ->select('id, sn, name, amount, created_at,expiresInDays, useEndDate, issueStartDate, issueEndDate, isAudited')
            ->where(['id' => $reward->ref_id])
            ->one();

        $userCoupon = UserCoupon::addUserCoupon($user, $couponType);
        $userCoupon->save(false);
        $awardR = Award::couponAward($user, $promo, $userCoupon, null, $reward)->save(false);
        return true === $awardR;
    }

    /**
     * 发积分
     */
    private function sendScore($reward, $promo)
    {
        if(empty($reward)){
            return false;
        }
        $user = $this->getAuthedUser();

        //根据sn获取奖品id
        $transaction = Yii::$app->db->beginTransaction();
        $pointSql = "update user set points = points + :points where id = :userId";
        $num = Yii::$app->db->createCommand($pointSql, [
            'points' => $reward->ref_amount,
            'userId' => $user->id,
        ])->execute();
        if ($num <= 0) {
            $transaction->rollBack();
            return false;
        }
        $user->refresh();

        $pointRecord = new PointRecord([
            'user_id' => $user->id,
            'sn' => TxUtils::generateSn('PROMO'),
            'final_points' => $user->points,
            'recordTime' => date('Y-m-d H:i:s'),
            'ref_type' => PointRecord::TYPE_PROMO,
            'ref_id' => $reward->id,
            'incr_points' => $reward->ref_amount,
            'userLevel' => $user->getLevel(),
            'remark' => '20181225活动获得',
        ]);
        $pointRecord->save(false);
        $award = Award::pointsAward($user, $promo, $pointRecord, null, $reward)->save(false);
        if (false === $award) {
            $transaction->rollBack();
            return false;
        };
        $transaction->commit();
        return true;
    }
    /**
     * 发现金红包
     * */
    private function sendCash($reward, $promo){
        $user = $this->getAuthedUser();
        $transfer = Transfer::initNew($user, $reward->ref_amount, ['promo_id'=>$promo->id]);
        if($transfer->save(false)){
            Award::transferAward($user, $promo,  $transfer, null, $reward)->save(false);
        }else{
            return false;
        }
        return true;
    }

    /**
     * 发实物
     * */
    private function sendGoods($reward, $promo){
        $user = $this->getAuthedUser();
        $goodsType = GoodsType::find()
            ->select('*')
            ->where(['id' => $reward->ref_id])
            ->one();
        $award = Award::goodsAward($user, $promo,  $goodsType, null, $reward)->save(false);
        return true === $award;
    }

    /**
     * 生成礼品数量
     */
    public function actionCreateGifts()
    {
        $redis = $this->redisConnect();
        $msg = '';

        foreach($this->pool as $k=> $v){
            $redis->hmset('gifts181225', $k, $v['num']);
            switch ($k){
                case '181225_points_20':
                    $msg .= ';20积分'.$v['num'].'个';
                    break;
                case '181225_CTCF_RP10':
                    $msg .= ';10现金'.$v['num'].'个';
                    break;
                case '181225_C20':
                    $msg .= ';20代金券'.$v['num'].'个';
                    break;
                case '181225_CTCF_XM':
                    $msg .= ';小米手环'.$v['num'].'个';
                    break;

            }
        }
        $redis->expire($this->hashNumKey, 2 * 60);
        echo '设置奖品数量成功'.$msg;
    }

    public function actionSet()
    {

        /*$promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181225']);
        $promoClass = new $promo->promoClass($promo);
        $ret = $promoClass->doAfterOrderSuccess(OnlineOrder::findOne(18));
        Yii::info('123', 'promo_log');
        exit;
        print_r($ret);
        exit;*/
        $redis = $this->redisConnect();
        $msg = '';
        foreach($this->pool as $k=> $v){
            $redis->hmset('gifts181225', $k, $v['num']);
            switch ($k){
                case '181225_points_20':
                    $msg .= ';20积分'.$v['num'].'个';
                    break;
                case '181225_CTCF_RP10':
                    $msg .= ';10现金'.$v['num'].'个';
                    break;
                case '181225_C20':
                    $msg .= ';20代金券'.$v['num'].'个';
                    break;
                case '181225_CTCF_XM':
                    $msg .= ';小米手环'.$v['num'].'个';
                    break;

            }
        }
        $redis->expire('gifts181225', 5);
        echo '设置奖品数量成功'.$msg;
    }

    public function actionGet()
    {
        $redis = $this->redisConnect();
        if ($redis->hexists('gifts181225', '181225_points_20')) {
            $redis->hincrby('gifts181225', '181225_points_20', -1);
            $data = $redis->hget('gifts181225', '181225_points_20');
            if (0 === $data) {
                $redis->hdel('gifts181225', '181225_points_20');
            }else{
                print_r($data);
            }
        } else {
            print_r($redis->hexists('gifts181225', '181225_points_20'));
        }
    }

}