<?php

namespace backend\modules\growth\controllers;

use backend\actions\ExcelPreviewAction;
use backend\controllers\BaseController;
use common\models\growth\PointsBatch;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\user\User;
use common\service\PointsService;
use common\utils\ExcelUtils;
use common\utils\SecurityUtils;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;
class PointsController extends BaseController
{
    public function actions()
    {
        return [
            'preview' => [
                'class' => ExcelPreviewAction::className(),
                'modelClass' => PointsBatch::className(),
                'maxCol' => 'E',
                'attributes' => ['publicMobile', 'idCard', 'isOnline', 'points', 'desc'],
                'backUrl' => '/growth/points/init',
            ]
        ];
    }

    //导入
    public function actionInit()
    {
        if (\Yii::$app->request->isPost) {
            $upload = UploadedFile::getInstanceByName('pointsFile');
            if (!is_null($upload) && !$upload->getHasError()) {
                $batchSn = rand(1, 1000) . time() . rand(1, 1000);
                $dir = __DIR__ . '/../../../runtime/tmp';
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $res = $upload->saveAs($dir . '/' . $batchSn, true);
                if ($res) {
                    return $this->redirect('/growth/points/preview?batchSn=' . $batchSn);
                }
            }
            return $this->redirect('/growth/points/init');
        }

        return $this->render('init');
    }

    //添加数据
    public function actionAdd($batchSn)
    {
        $file = __DIR__ . '/../../../runtime/tmp' . '/' . $batchSn;
        if (!file_exists($file)) {
            return $this->redirect('/growth/points/init');
        }
        $data = ExcelUtils::readExcelToArray($file, 'E');
        $time = date('Y-m-d H:i:s');
        $successCount = 0;
        foreach ($data as $value) {
            if (is_array($value)) {
                list($mobile, $idCard, $isOnline, $points, $desc) = $value;
                $points = intval($points);
                if (!empty($mobile) && !is_null($isOnline) && $points != 0) {
                    $isOnline = boolval($isOnline);
                    $safeIdCard = SecurityUtils::encrypt(trim($idCard));
                    $safeMobile = SecurityUtils::encrypt(trim($mobile));
                    if ($isOnline) {
                        $user = User::findOne(['safeMobile' => $safeMobile]);
                        if (is_null($user)) {
                            continue;
                        }
                    } else {
                        $user = OfflineUser::findOne(['idCard' => trim($idCard), 'mobile' => trim($mobile)]);
                        if (is_null($user)) {
                            continue;
                        }
                    }

                    $model = new PointsBatch([
                        'batchSn' => $batchSn,
                        'createTime' => $time,
                        'isOnline' => $isOnline,
                        'points' => $points,
                        'desc' => $desc,
                        'status' => 0,
                        'publicMobile' => $mobile,
                        'safeMobile' => $safeMobile,
                        'idCard' => $safeIdCard,
                        'user_id' => $user->id,
                    ]);
                    $res = $model->save();
                    if ($res) {
                        $successCount++;
                    }
                }
            }
        }
        unlink($file);
        if ($successCount > 0) {
            return $this->redirect('/growth/points/list?batchSn=' . $batchSn);
        }
        return $this->redirect('/growth/points/init');
    }

    //列表
    public function actionList($batchSn)
    {
        if (empty($batchSn)) {
            return $this->redirect('/growth/points/init');
        }
        $query = PointsBatch::find()->where(['batchSn' => $batchSn])->orderBy(['status' => SORT_DESC, 'id' => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        $notSendCount = PointsBatch::find()->where(['batchSn' => $batchSn, 'status' => 0])->count();
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'notSendCount' => $notSendCount,
        ]);
    }

    //确认
    public function actionConfirm($batchSn)
    {
        if (empty($batchSn)) {
            return $this->redirect('/growth/points/init');
        }
        $data = PointsBatch::find()->where(['batchSn' => $batchSn, 'status' => 0])->all();
        foreach ($data as $model) {
            if ($model instanceof PointsBatch) {
                $isOnline = $model->isOnline;
                if ($isOnline) {
                    $user = User::findOne(['safeMobile' => $model->safeMobile]);
                    if (is_null($user)) {
                        continue;
                    }
                } else {
                    $mobile = SecurityUtils::decrypt($model->safeMobile);
                    $user = OfflineUser::findOne(['mobile' => $mobile, 'idCard' => SecurityUtils::decrypt($model->idCard)]);
                    if (is_null($user)) {
                        continue;
                    }
                }
                $record = new PointRecord([
                    'ref_type' => PointRecord::TYPE_BACKEND_BATCH,
                    'ref_id' => $model->id,
                    'remark' => $model->desc
                ]);
                if ($model->points < 0) {
                    $record->decr_points = abs($model->points);
                } else {
                    $record->incr_points = $model->points;
                }
                $res = PointsService::addUserPoints($record, !$isOnline, $user);
                $model->status = $res ? 1 : 2;
                $model->save();
            }
        }
        return $this->redirect('/growth/points/list?batchSn=' . $batchSn);
    }
}