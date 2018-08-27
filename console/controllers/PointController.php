<?php
namespace console\controllers;

use common\lib\user\UserStats;
use common\models\mall\PointRecord;
use common\models\offline\OfflineOrder;
use common\models\offline\OfflineUser;
use common\models\user\User;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
use PHPExcel_IOFactory;
use yii\console\Controller;
use Yii;

class PointController extends Controller
{
    public function actionCancel($user_id = null)
    {
        $orders = OfflineOrder::find()
            ->where(['isDeleted' => false])
            ->andFilterWhere(['<', 'orderDate', '2017-01-01'])
            ->andWhere(['!=', 'valueDate', 'null'])
            ->orderBy(['user_id' => SORT_DESC])
            ->all();
        $lastUserId = null;
        $records = [];
        foreach ($orders as $order) {
            if ($order->user_id !== $lastUserId) {
                $lastUserId = $order->user_id;
                $records[$order->user_id]['user_id'] = $order->user_id;
                $records[$order->user_id]['points'] = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            } else {
                $records[$order->user_id]['points'] += max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            }
        }

        $err = [];
        $db = \Yii::$app->db;
        foreach ($records as $record) {
            //防撤销重复处理
            $canceled = PointRecord::find()
                ->where(['user_id' => $record['user_id']])
                ->andWhere(['isOffline' => true])
                ->andWhere(['ref_type' => PointRecord::TYPE_OFFLINE_ORDER_POINT_CANCEL])
                ->count();
            if ($canceled > 0 || $record['points'] <= 0) {
                continue;
            }

            try {
                $transaction = $db->beginTransaction();
                $user = OfflineUser::findOne($record['user_id']);
                $db->createCommand("UPDATE `offline_user` SET `points` = `points` - :points WHERE `id` = :userId", ['points' => $record['points'], 'userId' => $user->id])->execute();
                $user->refresh();
                $pointRecord = new PointRecord();
                $pointRecord->sn = TxUtils::generateSn('OFFCAN');
                $pointRecord->user_id = $user->id;
                $pointRecord->ref_type = PointRecord::TYPE_OFFLINE_ORDER_POINT_CANCEL;
                $pointRecord->decr_points = $record['points'];
                $pointRecord->final_points = $user->points;
                $pointRecord->recordTime = date('Y-m-d H:i:s');
                $pointRecord->isOffline = true;
                $pointRecord->userLevel = $user->level;
                $pointRecord->save();
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                $err[] = $record['user_id'];
            }
        }
        var_dump($err);
    }

    //导出温都所有用户的积分数值，线上线下分开
    //要素：用户姓名、手机号、积分数值
    public function actionExport()
    {
        $data[] = ['用户姓名', '手机号', '积分数值'];
        $onlinePoints = User::find()
            ->select('real_name as realName,safeMobile as mobile,points')
            ->where([
                'type' => 1,
                'status' => 1,
                'is_soft_deleted' => 0,
            ])
            ->andWhere(['>', 'points', 0])
            ->asArray()
            ->all();
        foreach ($onlinePoints as $onlinePoint) {
            $onlinePoint['mobile'] = SecurityUtils::decrypt($onlinePoint['mobile']);
            $data[] = $onlinePoint;
        }
        $offlinePoints = OfflineUser::find()
            ->select('realName,mobile,points')
            ->where('onlineUserId is null')
            ->andWhere(['>', 'points', 0])
            ->asArray()
            ->all();
        foreach ($offlinePoints as $offlinePoint) {
            $data[] = $offlinePoint;
        }
        $file = Yii::getAlias('@app/runtime/points_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($data);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }
}
