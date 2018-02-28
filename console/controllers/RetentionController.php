<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\growth\Retention;
use common\models\promo\TicketToken;
use common\models\stats\Perf;
use common\models\user\User;
use common\models\user\UserInfo;
use common\service\SmsService;
use common\utils\SecurityUtils;
use Wcg\Growth\Integration\Yii2Module\Model\ReferralSource;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\mutex\FileMutex;

class RetentionController extends Controller
{
    public function actionCall()
    {
        $cmdLock = new FileMutex();
        if (!$cmdLock->acquire('retention')) {
            exit;
        }

        $retentions = Retention::find()
            ->where(['status' => Retention::STATUS_INIT])
            ->andWhere(['in', 'tactic_id', [1, 2, 3]])
            ->limit(10)
            ->all();

        $db = Yii::$app->db;
        $platAmount = $this->getPlatAmount();
        foreach ($retentions as $retention) {
            $sql = "update retention set status = :status,startTime = :startTime where id = :id and status = 'init'";
            $affectedRows = $db->createCommand($sql, [
                'status' => Retention::STATUS_START,
                'startTime' => date('Y-m-d H:i:s'),
                'id' => $retention->id,
            ])->execute();
            if ($affectedRows > 0) {
                //判断如果今天存在订单，则不再发送短信
                $userInfo = UserInfo::find()
                    ->where(['lastInvestDate' => date('Y-m-d')])
                    ->andWhere(['user_id' => $retention->user_id])
                    ->one();
                if (null !== $userInfo) {
                    $sql = "update retention set status = :status where id = :id";
                    $db->createCommand($sql, [
                        'id' => $retention->id,
                        'status' => Retention::STATUS_CANCEL,
                    ])->execute();
                    continue;
                }
                $couponConfig = $this->getCouponConfig($retention->tactic_id);
                try {
                    if (!empty($couponConfig)) {
                        $amount = 0;
                        $user = User::findOne($retention->user_id);
                        foreach ($couponConfig as $sn) {
                            //发代金券
                            $couponType = CouponType::findOne(['sn' => $sn]);
                            if (is_null($user) || is_null($couponType)) {
                                throw new \Exception('没有找到对应参数');
                            }
                            UserCoupon::addUserCoupon($user, $couponType)->save(false);
                            $amount += $couponType->amount;
                        }
                        //发短信
                        $message = [
                            $platAmount,
                            $amount,
                            $this->getUrl($retention->tactic_id),
                            Yii::$app->params['platform_info.contact_tel'],
                        ];
                        SmsService::send(SecurityUtils::decrypt($user->safeMobile), 184056, $message, $user);
                    }
                } catch (\Exception $ex) {
                    $sql = "update retention set status = :status where id = :id";
                    $db->createCommand($sql, [
                        'id' => $retention->id,
                        'status' => Retention::STATUS_FAIL,
                    ])->execute();
                }
            }
        }
        $cmdLock->release('retention');
    }

    private function getCouponConfig($tacticId)
    {
        $couponConfig = [];
        if (1 === $tacticId) {
            $couponConfig = [
                'retention_50000_50',
                'retention_10000_20',
            ];
        } elseif (2 === $tacticId) {
            $couponConfig = [
                'retention_1000_8',
                'retention_10000_30',
            ];
        } elseif (3 === $tacticId) {
            $couponConfig = [
                'retention_1000_8',
                'retention_5000_20',
            ];
        }

        return $couponConfig;
    }

    private function getPlatAmount()
    {
        $cache = Yii::$app->db_cache;
        $key = 'index_stats';
        if (!$cache->get($key)) {
            $statsData = Perf::getStatsForIndex();
            $cache->set($key, $statsData, 600);   //缓存十分钟
        }
        $perf = $cache->get($key);

        return floor($perf['totalTradeAmount']  / 100000000) . '亿';
    }

    private function getUrl($tacticId)
    {
        $url = Yii::$app->params['clientOption']['host']['wap'];
        $key = '';
        $referral = null;
        if (1 === $tacticId) {
            $key = 'XhY3Hc';
        } elseif (2 === $tacticId) {
            $key = 'xpQTVd';
        } elseif (3 === $tacticId) {
            $key = '7FVmmP';
        }
        if ($key) {
            $referral = ReferralSource::findOne(['key' => $key]);
        }

        return null !== $referral ? $referral->getReferralURL() : $url;
    }

    /**
     * 导出指定时间段且当前理财资产为0，可用余额为0，认购次数1次以上的客户信息
     *
     * 导出项：用户ID、注册时间、姓名、联系方式、可用余额、投资成功金额、性别，生日，年龄
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     *
     * @return int
     */
    public function actionListExport($startDate, $endDate)
    {
        //获得指定时间段且当前理财资产为0，可用余额为0，认购次数1次以上的客户信息
        $sql = "select 
o.uid,count(o.id) investCount,u.real_name,u.safeMobile,from_unixtime(u.created_at) createTime,sum(o.order_money) as investMoney,ua.available_balance,u.safeIdCard,u.birthdate
from online_order o 
inner join user u on u.id = o.uid
inner join user_account ua on ua.uid = o.uid
where date(from_unixtime(o.order_time)) >= '2017-07-17'
and date(from_unixtime(o.order_time)) <= '2017-12-31'
and o.uid not in (
	select distinct(uid) from online_repayment_plan where status = 0
)
and o.status = 1 
and ua.available_balance <= 0
group by o.uid
having investCount > 1";
        $userInfo = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        //判断是否有符合条件的用户
        if (empty($userInfo)) {
            $this->stdout('无符合条件的用户信息');
            return self::EXIT_CODE_ERROR;
        }

        $exportContent = [];
        foreach ($userInfo as $k => $ui) {
            $exportContent[$k]['uid'] = $ui['uid'];
            $exportContent[$k]['realName'] = $ui['real_name'];
            $age = date('Y') - (int) substr($ui['birthdate'], 0, 4);
            $idCard = SecurityUtils::decrypt($ui['safeIdCard']);
            $exportContent[$k]['mobile'] = '\''.SecurityUtils::decrypt($ui['safeMobile']);
            $exportContent[$k]['createTime'] = $ui['createTime'];
            $exportContent[$k]['investMoney'] = $ui['investMoney'];
            $exportContent[$k]['availableBalance'] = $ui['available_balance'];
            $exportContent[$k]['gender'] = intval(substr($idCard, -2, 1) % 2);
            $exportContent[$k]['birthDate'] = $ui['birthdate'];
            $exportContent[$k]['age'] = $age;
        }

        $file = Yii::getAlias('@app/runtime/Retention_'.$startDate.'_'.$endDate .'_'. date('YmdHis').'.xlsx');
        $exportData[] = ['用户ID', '姓名', '手机号', '注册时间', '投资成功金额', '可用余额', '性别', '生日', '年龄'];
        $exportData = array_merge($exportData, $exportContent);
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 指定时间段且当前理财资产为0，可用余额为0，认购次数1次以上的客户发放指定代金券
     *
     * @param string $startDate 开始日期
     * @param string $endDate 结束日期
     *
     * @return int
     */
    public function actionSendCoupon($startDate, $endDate)
    {
        //获得指定时间段且当前理财资产为0，可用余额为0，认购次数1次以上的客户信息
        $sql = "select 
o.uid,count(o.id) investCount
from online_order o 
inner join user u on u.id = o.uid
inner join user_account ua on ua.uid = o.uid
where date(from_unixtime(o.order_time)) >= '2017-07-17'
and date(from_unixtime(o.order_time)) <= '2017-12-31'
and o.uid not in (
	select distinct(uid) from online_repayment_plan where status = 0
)
and o.status = 1 
and ua.available_balance <= 0
group by o.uid
having investCount > 1";
        $userInfos = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        //判断是否有符合条件的用户
        if (empty($userInfos)) {
            $this->stdout('无符合条件的用户ID集合');
            return self::EXIT_CODE_ERROR;
        }

        //获得发送代金券的用户及代金券sn
        $userIds = ArrayHelper::getColumn($userInfos, 'uid');
        $users = User::find()
            ->where(['in', 'id', $userIds])
            ->all();
        $couponSns = $this->getCouponSns();
        $couponTypes = CouponType::find()
            ->where(['in', 'sn', $couponSns])
            ->all();

        //发放代金券
        $this->stdout('共有用户'.count($users).'人待发放优惠券');
        foreach ($users as $user) {
            foreach ($couponTypes as $couponType) {
                $tokenKey = 'retention_'.$user->id.'_'.$couponType->id;
                TicketToken::initNew($tokenKey)->save(false);
                UserCoupon::addUserCoupon($user, $couponType)->save(false);
            }
        }

        $this->stdout('代金券发放完毕');
        return self::EXIT_CODE_NORMAL;
    }

    private function getCouponSns()
    {
        return [
            '180228_retention_100',
            '180208_retention_500',
        ];
    }
}
