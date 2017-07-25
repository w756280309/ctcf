<?php

namespace console\controllers;

use common\models\code\Voucher;
use common\models\promo\TicketToken;
use common\models\user\CheckIn;
use common\models\mall\PointRecord;
use common\models\user\User;
use common\service\SmsService;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

Class CheckinController extends Controller
{
    /**
     * 根据已有的PointRecord记录补充所有的签到记录
     */
    public function actionInit()
    {
        $pointRecords = PointRecord::find()
            ->select(['user_id', 'recordTime'])
            ->where(['isOffline' => false])
            ->andWhere(['ref_type' => PointRecord::TYPE_MALL_INCREASE])
            ->orderBy([
                'user_id' => SORT_ASC,
                'recordTime' => SORT_ASC,
            ])
            ->asArray()
            ->all();

        $this->stdout('当前积分流水数量：' . count($pointRecords));

        $num = 0;
        $lastUserId = null;
        foreach ($pointRecords as $pointRecord) {
            $user = User::findOne($pointRecord['user_id']);
            if (null !== $user) {
                if ($lastUserId !== $user->id) {
                    $num++;
                    $lastUserId = $user->id;
                }
                CheckIn::check($user, (new \DateTime($pointRecord['recordTime'])), 30, false);
            }
        }

        $this->stdout('涉及的用户数' . $num);
        return self::EXIT_CODE_NORMAL;
    }

    public function actionSupplement()
    {
        $query = User::find()
            ->where(['type' => User::USER_TYPE_PERSONAL])
            ->andWhere(['is_soft_deleted' => false]);

        $num = $query->count();
        $users = $query->all();

        foreach ($users as $user) {
            CheckIn::check($user, (new \DateTime()));
        }

        $finalNum = 0;
        $finalNum = CheckIn::find()->count();

        $this->stdout($num === $finalNum);
        return self::EXIT_CODE_NORMAL;
    }

    //部分用户在我们修复数据时候进行签到，导致连续签到数据及签到积分数据错误 2017-04-13
    public function actionRepair()
    {
        $userIds = [1112, 9096, 972, 3843, 2453, 3034, 3797, 9501,8823,10797];//2017-04-13 出问题用户
        $checkRecord = CheckIn::find()
            ->where(['in','user_id', $userIds])
            ->andWhere(['checkDate' => '2017-04-13'])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            foreach ($checkRecord as $check) {
                $userId = $check->user_id;
                $this->stdout("正在修复用户{$userId}的数据". PHP_EOL);
                //判断用户是否在2017-04-12签到
                $lastCheck = CheckIn::find()->where([
                    'user_id' => $userId, 'checkDate' => '2017-04-12',
                ])->one();
                if (is_null($lastCheck)) {
                    $this->stdout('没有找到2017-04-12签到记录， 说明2017-04-13是第一次签到，数据无需修复'. PHP_EOL);
                    continue;
                }

                //找到用户13号积分流水数据 (当用户已经有积分流水时候，再次点击签到会出现两条记录)
                $pointRecords = PointRecord::find()
                    ->where([
                        'user_id' => $userId,
                        'date(recordTime)' => '2017-04-13',
                        'ref_type' => 'check_in',
                    ])->orderBy(['id' => SORT_ASC])->all();
                $count = count($pointRecords);

                $this->stdout("找到用户{$userId} {$count}条13号签到积分流水". PHP_EOL);
                if (empty($pointRecords)) {
                    continue;
                }

                //修复13号数据
                $check->streak = $lastCheck->streak + 1;//没有超过30天数据
                $check->lastCheckDate = $lastCheck->checkDate;
                if (!$check->save()) {
                    throw new \Exception("修复用户{$userId} 13号签到记录错误");
                }
                $this->stdout("更新用户{$userId} 13号签到记录信息". PHP_EOL);

                $award = CheckIn::getAward($check);
                $realPoints = $award['points'];//应该增加的积分
                $pointRecord = $pointRecords[0];
                $pointRecordId = $pointRecord->id;//13号第一条签到流水ID
                $finalPoints = $pointRecord->final_points;//13号第一条签到流水时候用户的最终积分
                $userPointsChange = 0;//用户积分变动值
                $points = $pointRecord->incr_points;//13好实际增加积分
                $this->stdout("用户{$userId} 13号应得签到积分{$realPoints}, 实际得到{$points}". PHP_EOL);
//修复第一条流水
                if ($realPoints > $points) {//正常应得积分只会大于等于第一条积分流水的积分
                    $userPointsChange = $realPoints - $points;
                    $finalPoints = $pointRecord->final_points + $userPointsChange;
                    $pointRecord->incr_points = $realPoints;
                    $pointRecord->final_points = $finalPoints;
                    if (!$pointRecord->save()) {
                        throw new \Exception("修复用户{$userId} 13号积分流水时候出错");
                    }
                    $this->stdout("更新用户{$userId} 13号第一条签到流水". PHP_EOL);
                }

                //删除当天重复流水
                if ($count > 1) {
                    for ($i = 1; $i < $count; $i++) {
                        $pointRecord = $pointRecords[$i];
                        $userPointsChange = $userPointsChange - $pointRecord->incr_points;
                        if(!$pointRecord->delete()) {
                            throw new \Exception("删除用户{$userId} 13号第".($i +1)."条签到流水失败");
                        }
                        $this->stdout("删除用户{$userId} 13号第".($i +1)."条签到流水, 当条流水积分{$pointRecord->incr_points}". PHP_EOL);
                    }
                }

                //修复14号数据
                //判断用户14号是否签到
                $nextCheck = CheckIn::find()->where([
                    'user_id' => $userId, 'checkDate' => '2017-04-14',
                ])->one();
                if (!is_null($nextCheck)) {
                    $this->stdout("用户{$userId} 14号进行了签到". PHP_EOL);
                    $nextCheck->lastCheckDate = $check->checkDate;
                    $nextCheck->streak = $check->streak + 1;
                    if (!$nextCheck->save()) {
                        throw new \Exception("修复用户{$userId} 14号签到记录错误");
                    }
                    $this->stdout("修复用户{$userId} 14号签到记录". PHP_EOL);
                    $pointRecord = PointRecord::findOne([
                        'user_id' => $userId,
                        'date(recordTime)' => '2017-04-14',
                        'ref_type' => 'check_in',
                    ]);
                    if (is_null($pointRecord)) {
                        throw new \Exception("用户{$userId} 14号签到后没有找到积分流水");
                    }
                    $award = CheckIn::getAward($nextCheck);
                    $this->stdout("用户{$userId} 14号签到应得积分{$award['points']}, 实际给了{$pointRecord->incr_points}". PHP_EOL);
                    if ($award['points'] > $pointRecord->incr_points) {
                        $userPointsChange += $award['points'] - $pointRecord->incr_points;
                        $pointRecord->incr_points = $award['points'];
                        if (!$pointRecord->save()) {
                            throw new \Exception("更新用户{$userId} 第14号积分流水失败");
                        }
                        $this->stdout("修复用户{$userId} 14号签到积分流水". PHP_EOL);
                    }
                }

                //修改13号第一条签到流水之后流水的final_points
                $otherPointRecords = PointRecord::find()->where(['user_id' => $userId])->andWhere(['>', 'id', $pointRecordId])->all();
                foreach ($otherPointRecords as $pointRecord) {
                    $finalPoints = $finalPoints + $pointRecord->incr_points - $pointRecord->decr_points;
                    if ($finalPoints != $pointRecord->final_points) {
                        if (!$pointRecord->save()) {
                            throw new \Exception("修复用户{$userId} ID为{$pointRecord->id}的final_points失败");
                        }
                        $this->stdout("修复用户{$userId} 积分流水{$pointRecord->id} 的final_points". PHP_EOL);
                    }
                }

                //更新用户最终积分
                $user = User::findOne($userId);
                if ($user->points + $userPointsChange != $finalPoints) {
                    throw new \Exception("用户{$userId}当前积分是{$user->points}, 变动积分是{$userPointsChange}, 根据积分流水推到出的最终积分是{$finalPoints}");
                }
                $this->stdout("修复用户{$userId} 最终积分{$finalPoints}". PHP_EOL);
                $user->points = $finalPoints;
                if(!$user->save(false)) {
                    throw new \Exception("更新用户{$userId}积分失败");
                }

                $this->stdout(PHP_EOL . PHP_EOL);
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            $this->stdout("修复错误：错误信息" . $ex->getMessage(). PHP_EOL);
        }
    }

    /**
     * 签到用户召回
     *
     * @param string $points 待发放积分奖励
     *
     * @return void
     */
    public function actionRetention($points = '10')
    {
        //筛符合的用户
        $query = CheckIn::find()
            ->select('user_id')
            ->distinct();
        $cQuery = clone $query;
        $allCheckIns = $query->column();
        $fourteenDaysAgo = new \DateTime('-14 day');
        $hasCheckedInFourteenDays = $cQuery->where([
            '>=', 'createTime', $fourteenDaysAgo->format('Y-m-d H:i:s')
        ])->column();
        $userIds = array_diff($allCheckIns, $hasCheckedInFourteenDays);
        $hasSendVouchers = Voucher::find()
            ->where(['ref_type' => PointRecord::REF_TYPE_CHECK_IN_RETENTION])
            ->all();
        $hasSendUserIds = ArrayHelper::getColumn($hasSendVouchers, 'user_id');
        $voucherUserIds = array_diff($userIds, $hasSendUserIds);

        //添加voucher
        if (empty($voucherUserIds)) {
            $this->stdout('未筛选到用户');
            return;
        }

        $voucherUsers = User::find()
            ->where(['in', 'id', $voucherUserIds])
            ->all();
        $db = \Yii::$app->db;
        $sendUsers = [];
        $nowTime = (new \DateTime())->format('Y-m-d H:i:s');
        $expireTime = (new \DateTime('+30 day'))->format('Y-m-d H:i:s');
        foreach ($voucherUsers as $user) {
            try {
                $transaction = $db->beginTransaction();
                //新建一个voucher
                $voucher = new Voucher();
                $voucher->ref_type = PointRecord::REF_TYPE_CHECK_IN_RETENTION;
                $voucher->user_id = $user->id;
                $voucher->isRedeemed = false;
                $voucher->createTime = $nowTime;
                $voucher->expireTime = $expireTime;
                $voucher->isOp = true;
                $voucher->amount = $points;
                $voucher->save(false);
                //防重复添加
                $ticketToken = new TicketToken();
                $ticketToken->key = $voucher->ref_type . '.' . $user->id;
                $ticketToken->save(false);
                $transaction->commit();
                $sendUsers[] = $user;
            } catch (\Exception $ex) {
                $transaction->rollBack();
                continue;
            }
        }
        //成功后发送短信
        $this->stdout('待发送短信的用户共'.count($sendUsers).'人');
        if (!empty($sendUsers)) {
            foreach ($sendUsers as $user) {
                SmsService::send($user->mobile, '191643', [
                    rtrim(\Yii::$app->params['clientOption']['host']['wap'], '/') . '/' . 'user/checkin',
                ], $user);
            }
        }
        $this->stdout('发放完成');
    }
}
