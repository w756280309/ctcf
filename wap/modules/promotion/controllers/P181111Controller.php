<?php
/**
 * Created by PhpStorm.
 * User: cz
 * Date: 2018/10/22
 * Time: 16:23
 */
namespace wap\modules\promotion\controllers;

use Yii;
use common\models\order\OnlineOrder;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\Award;
use common\models\promo\Reward;
use common\models\mall\PointRecord;
use yii\web\Response;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;

class P181111Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    private $promoType = ['1'=> '111积分','2'=> '11元现金', '3'=> '1.1%加息券', '4'=> '11元满减券'];
    private $arrRewardSn = ['1'=> '181111_P111', '3'=> '181111_BC11', '4'=> '181111_C11'];
    private $beginY = '20181105';
    private $beginTime = '10:00:00';
    private $endTime = '23:59:59';

    /**
     * redis连接
     * */
    private function redisConnect()
    {
        $redis = Yii::$app->redis_session;
        return $redis;
    }

    /**
     * 18年双十一活动首页
    */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181111']);
        $user = $this->getAuthedUser();

        $isLoggedIn = null !== $user;
        $isClick = $this->getTimeSection();
        $isGet = 0;
        $arrKeys = [];
        $awardlist = [];
        if($isLoggedIn){
            $uid = $user->getId();
            $redis = $this->redisConnect();
            $arrKeys = $redis->keys('promo_queue201811*');
            $isGet = !empty($redis->get('saveAward'.$uid) && is_numeric($redis->get('saveAward'.$uid))) ? 1 : 0;

            //获奖列表
            $awardlist = $this->getAwardList();
        }

        return $this->render('index', [
            'awardlist'=> $awardlist,
            'arrKeys'=>$arrKeys,
            'isClick'=>$isClick,
            'isGet'=>$isGet,
            'isLoggedIn'=>$isLoggedIn,
        ]);
    }


    /**
     * 每天上午10点跑这个脚本创建当天要抽的奖品
    */
    public function actionCaward()
    {
        $date = date('Ymd', time());
        $key = 'promo_queue201811';
        $sql = 'select id from promo_queue201811 where promodate='.$date.'  and  isvalid=1 and userid is null';
        $arrIds = Yii::$app->db->createCommand($sql)->queryColumn();
        if(empty($arrIds)){
            $file = Yii::getAlias('@app/runtime/logs/20181111_caward.txt');
            file_put_contents($file, date('Y-m-d H:i:s') . "| sql语句:".$sql, FILE_APPEND);
            return false;
        }
        $redis = $this->redisConnect();
        //先清除用户获奖列表
        $userAwardR = $redis->keys('saveAward*');
        $delU = 0;
        if(!empty($userAwardR)){
            foreach ($userAwardR as $k=> $v){
                $delU ++;
                $redis->del($v);
            }
        }
        $i = 0;
        foreach ($arrIds as $k=> $v){
            if(!empty($redis->exists($key.':'.$v))){
                continue;
            }
            $i ++;
            $redis->set($key.':'.$v, $v);
            $redis->expire($key.':'.$v, 13*60*60);
        }
        echo  '共生成'.$i.'个成功'.PHP_EOL.'获奖列表清除'.$delU.'个';
    }

    /**
     * 抢红包
    */
    public function actionOpenactive()
    {
        $user = $this->getAuthedUser();
        if(null === $user){
            return ['code'=> 201, 'message'=> '请登录'];
        }
        
        if(intval(date("H",time()))<10){
        	return ['code'=>205, 'message'=> '今日活动还未开始,请耐心等待~'];
        }
        
        $user_id = $user->getId();
        $redis = $this->redisConnect();
        if(empty($redis->keys('promo_queue201811*'))){
            return ['code'=>205, 'message'=> '今日红包已发放完,明天再来吧~'];
        }

        if(!empty($redis->get('saveAward'.$user_id) && is_numeric($redis->get('saveAward'.$user_id)))){
            return ['code'=> 203, 'message'=> '您今天已经参加了抽奖活动!'];
        }

        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181111']);
        //区分客户类型投过资的为老客户
        $data = OnlineOrder::find()
            ->select('count(id) as count')
            ->where(['status' => OnlineOrder::STATUS_SUCCESS, 'uid' => $user_id])
            ->asArray()
            ->one();
        $user_type = 1;
        if(!empty($data['count'])){
            $user_type = 2;
        }

        $date = date('Ymd', time());

        try{
            //先更新数据，然后根据用户id和时间获取数据
            $sql = 'update promo_queue201811 set userid='.$user_id.', updatetime=now() where promodate=:pDate and isvalid=1 and usertype in ( 0 ,'.$user_type.') and userid is null and not exists (select 1 from (select 1 from promo_queue201811 where promodate=:pDate and userid=:userId) u) limit 1';
            $upt = Yii::$app->db->createCommand($sql, [
                'userId' => $user_id,
                'pDate' => $date,
            ])->execute();
            if(empty($upt)){
                return ['code'=>206, 'message'=> '很遗憾，红包未抢到！'];
            }
            $sql = 'select id, userid, promotype, couponid from promo_queue201811 where userid=:userId and promodate=:pDate and isvalid = 1 limit 1';
            $arrData = Yii::$app->db->createCommand($sql, [
                'userId' => $user_id,
                'pDate' => $date,
            ])->queryOne();

            $res = false;
            switch($arrData['promotype']){
                case 1://积分
                    $res = $this->sendScore($arrData, $promo);
                    break;
                case 3://加息券，满减券
                case 4:
                    $res = $this->sendTicket($arrData, $promo);

                    break;
            }
            if(false === $res || empty($arrData['id'])){
                return ['code'=> 204, 'message'=> '发放失败'];
            }
            //开始清除队列
            if($redis->exists('promo_queue201811:'.$arrData['id'])){
                $redis->del('promo_queue201811:'.$arrData['id']);
            }
            $redis->set('saveAward'.$user_id, $arrData['id']);//设置12小时
            $redis->expire('saveAward'.$user_id, 13*60*60);
            if (Yii::$app->request->isAjax) {
                return [
                    'code'=> 200,
                    'message'=> '发放成功',
                    'data' => [
                        'promotype' => $this->promoType[$arrData['promotype']],
                    ]
                ];
            }
        }catch (\Exception $ex) {
            return ['code'=> 202, 'message'=> '发放失败'.$ex->getMessage()];
        }
    }

    /**
     * 发放加息券，满减券
    */
    private function sendTicket($param = [], $promo)
    {
        if(empty($param['userid']) || empty($param['couponid'])){
            throw new \Exception('缺少用户id：userid或者券id：couponid', 1);
        }

        $user = $this->getAuthedUser();
        $couponType = CouponType::find()
            ->select('id, sn, name, amount, created_at,expiresInDays, useEndDate, issueStartDate, issueEndDate, isAudited')
            ->where(['id' => $param['couponid']])
            ->one();

        //根据sn获取奖品id
        $reward = Reward::fetchOneBySn($this->arrRewardSn[$param['promotype']]);

        $userCoupon = UserCoupon::addUserCoupon($user, $couponType);
        $userCoupon->save(false);
        $awardR = Award::couponAward($user, $promo, $userCoupon, null, $reward)->save(false);
        if (false === $awardR) {
            throw new PromoException($promo, $user, '优惠券奖励记录保存失败', 22);
        };
        return true;
    }

    /**
     * 发积分
    */
    private function sendScore($param = [], $promo)
    {
        $user = $this->getAuthedUser();
        if(empty($param['userid'])){
            throw new \Exception('缺少用户id：userid', 1);
        }
        //根据sn获取奖品id
        $reward = Reward::fetchOneBySn($this->arrRewardSn[$param['promotype']]);
        $transaction = Yii::$app->db->beginTransaction();
        $pointSql = "update user set points = points + :points where id = :userId";
        $num = Yii::$app->db->createCommand($pointSql, [
            'points' => $reward->ref_amount,
            'userId' => $param['userid'],
        ])->execute();
        if ($num <= 0) {
            $transaction->rollBack();
            throw new PromoException($promo, $user, '1004:更新用户积分失败', 22);
        }
        $user->refresh();

        $pointRecord = new PointRecord([
            'user_id' => $param['userid'],
            'sn' => TxUtils::generateSn('PROMO'),
            'final_points' => $user->points,
            'recordTime' => date('Y-m-d H:i:s'),
            'ref_type' => PointRecord::TYPE_PROMO,
            'ref_id' => $reward->id,
            'incr_points' => $reward->ref_amount,
            'userLevel' => $user->getLevel(),
            'remark' => '20181111活动获得',
        ]);
        $pointRecord->save(false);
        $award = Award::pointsAward($user, $promo, $pointRecord, null, $reward)->save(false);
        if (false === $award) {
            $transaction->rollBack();
            throw new PromoException($promo, $user, '积分奖励记录保存失败', 22);
        };
        $transaction->commit();
        return true;
    }

    /**
     * 获取红包列表
    */
    public function getAwardList(){
        $user = $this->getAuthedUser();
        $arrData = [];
        if($user){
            $sql = 'select a.*, b.name, b.amount from promo_queue201811 as a left join coupon_type as b on a.couponid=b.id where a.userid=:userId and a.isvalid = 1';
            $arrData = Yii::$app->db->createCommand($sql, [
                'userId' => $user->getId(),
            ])->queryAll();

            if(!empty($arrData)){
                array_walk($arrData, function(&$value, $k){
                    $value['sname'] = $this->promoType[$value['promotype']];
                });
            }
        }
        return $arrData;
    }

    /**
     *检查活动状态
     */
    private function checkPromo($user)
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_181111']);
        $promoStatus = null;
        try {
            $promo->isActive($user, time());
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        if (!is_null($promoStatus)) {
            if ($promoStatus == 1) {
                $status = ['code' => 0, 'message' => '活动未开始'];
            } else {
                $status = ['code' => 2, 'message' => '活动已结束'];
            }
        } else {
            $status = ['code' => 1, 'message' => '活动正常'];
        }
        return $status;
    }

    private function getActiveErrorInfo(){
        return [
            '200'=> ['code'=> 200, 'message'=> '抢到了'],
        ];
    }

    /**
     * 判断在某个时间段内
    */
    private function getTimeSection()
    {
        $checkDayStr = date('Y-m-d ',time());
        $timeBegin = strtotime($checkDayStr.$this->beginTime);
        $timeEnd = strtotime($checkDayStr.$this->endTime);
        $curr_time = time();

        if($curr_time >= $timeBegin && $curr_time <= $timeEnd){
            return true;
        }
        return false;
    }

}
