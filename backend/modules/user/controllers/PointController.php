<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\user\User;
use common\utils\TxUtils;
use Yii;

class PointController extends BaseController
{
    /**
     * 发放积分.
     */
    public function actionAdd($userId, $isOffline = 0, $backUrl = false)
    {
        $points =  $isOffline ? OfflineUser::findOne($userId) : User::findOne($userId);
        if (empty($points->id) || !in_array($isOffline, ['0', '1'])) {
            throw $this->ex404();
        }

        $PointRecord = new PointRecord();
        if ($PointRecord->load(Yii::$app->request->post())
            && $PointRecord->validate()
            && $this->validatePoint($PointRecord)
        ) {
            $result = $this->faFang($PointRecord, $userId, $isOffline, $points);
            if ($result) {
                if ($backUrl) {
                    return $this->redirect($backUrl);
                }
                $this->redirect($isOffline ? '/user/offline/detail?id='.$userId : '/user/user/detail?id='.$userId.'&type=1');
            }
        }
        return $this->render('add', ['userId' => $userId, 'PointRecord' => $PointRecord, 'isOffline' => $isOffline, 'backUrl' => $backUrl]);
    }

    /**
     * 验证表单信息.
     */
    private function validatePoint($PointRecord)
    {
        if (empty($PointRecord->remark)) {
            $PointRecord->addError('remark', '请填写发放积分描述!');
            return false;
        }
        return true;
    }

    private function faFang($PointRecord, $userId, $isOffline, $points)
    {
        $transaction = Yii::$app->db->beginTransaction();
        if ($PointRecord->incr_points < 0) {
            $PointRecord->decr_points = abs($PointRecord->incr_points);
            $PointRecord->incr_points = 0;
            if ($points->points < $PointRecord->decr_points){
                $PointRecord->decr_points = $points->points;
            }
        }
        try {
            //point_record表插入一条数据
            $PointRecord->user_id = $userId;
            $PointRecord->ref_type = PointRecord::TYPE_POINT_FA_FANG;
            $PointRecord->sn = $isOffline ? TxUtils::generateSn('OFF') : TxUtils::generateSn('PR');
            $PointRecord->recordTime = date("Y-m-d H:i:s");
            $PointRecord->isOffline = $isOffline;
            if ($PointRecord->decr_points > 0) {
                $PointRecord->final_points = bcsub($points->points, $PointRecord->decr_points, 0);
            } else {
                $PointRecord->final_points = bcadd($points->points, $PointRecord->incr_points, 0);
            }
            $PointRecord->save(false);
            //更新对应的用户表里的points字段
            if ($PointRecord->decr_points > 0) {
                Yii::$app->db->createCommand('update '.($isOffline ? 'offline_user' : 'user') .' set points = points - :point where id = :id', ['point' => $PointRecord->decr_points, 'id' => $userId])->execute();
            } else {
                Yii::$app->db->createCommand('update '.($isOffline ? 'offline_user' : 'user') .' set points = points + :point where id = :id', ['point' => $PointRecord->incr_points, 'id' => $userId])->execute();
            }
            //去adminlog里记录操作
            AdminLog::initNew($PointRecord)->save(false);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }
}
