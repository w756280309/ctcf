<?php

namespace console\controllers;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\growth\Retention;
use common\models\stats\Perf;
use common\models\user\User;
use common\models\user\UserInfo;
use common\service\SmsService;
use common\utils\SecurityUtils;
use Wcg\Growth\Integration\Yii2Module\Model\ReferralSource;
use Yii;
use yii\console\Controller;
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
                            Yii::$app->params['contact_tel'],
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
}
