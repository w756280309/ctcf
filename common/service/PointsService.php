<?php

namespace common\service;


use common\models\adminuser\AdminLog;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\user\User;
use common\utils\TxUtils;
use Yii;

/**
 * 积分服务
 */
class PointsService
{
    /**
     * 给用户发积分
     *
     * @param PointRecord       $pointRecord  包含ref_type、incr_points的PointRecord
     * @param bool $isOffline   $isOffline     必须和$user类型对应
     * @param User|OfflineUser  $user
     * @param string            $dateTime       积分流水时间
     * @return bool
     */
    public static function addUserPoints(PointRecord $pointRecord, $isOffline, $user, $dateTime = null)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (
                empty($pointRecord->ref_type)
                || is_null($user)
                || empty($user->id)
            ) {
                throw new \Exception('参数不合法');
            }
            if ($isOffline) {
                if (!$user instanceof OfflineUser) {
                    throw new \Exception('线下用户必须是OfflineUser类型');
                }
                $sn = TxUtils::generateSn('OFF');
                $table = 'offline_user';
            } else {
                if (!$user instanceof User) {
                    throw new \Exception('线上用户必须是User类型');
                }
                $sn = TxUtils::generateSn('PR');
                $table = 'user';
                $pointRecord->userLevel = $user->level;
            }
            //用户积分判断
            if ($pointRecord->decr_points > 0) {
                if ($user->points == 0) {
                    $points = 0;
                } else if ($user->points < $pointRecord->decr_points) {
                    $points = $user->points;
                } else {
                    $points = 0 - $pointRecord->decr_points;
                }
            } else {
                $points = $pointRecord->incr_points;
            }
            //更新对应的用户表里的points字段
            $sql = 'update ' . $table . ' set points = points + :points where id = :userId';
            $res = Yii::$app->db->createCommand($sql, ['points' => $points, 'userId' => $user->id])->execute();
            if ($res === false) {
                throw new \Exception('更改用户积分失败');
            }

            $user->refresh();
            $pointRecord->user_id = $user->id;
            $pointRecord->sn = $sn;
            $pointRecord->final_points = $user->points;
            $pointRecord->recordTime = empty($dateTime) ? date("Y-m-d H:i:s") : $dateTime;
            $pointRecord->isOffline = $isOffline;
            $res = $pointRecord->save(false);
            if (!$res) {
                throw new \Exception('添加用户积分记录失败');
            }

            //去AdminLog里记录操作
            AdminLog::initNew($pointRecord)->save(false);

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
}