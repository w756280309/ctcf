<?php
namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use Wcg\Http\HeaderUtils;
use yii\data\Pagination;

class IssuerController extends BaseController
{
    /**
     * 立合旺通统计列表.
     * 1. 每页显示20条记录;
     */
    public function actionLhList()
    {
        $record = Issuer::getIssuerRecords(1, 20);

        return $this->render('list', $record);
    }

    /**
     * 发行方统计数据导出.
     * 当面没有验证登录者身份,此处优化后期等融资方较多时,再行优化;
     */
    public function actionExport($id)
    {
        $id = intval($id);
        $linkFile = rtrim(\Yii::getAlias('@backend'), '/') . '/web/data/issuer_' . $id . '.xls';
        if ($linkFile && file_exists($linkFile)) {
            $file = readlink($linkFile);
            if ($file && file_exists($file)) {
                $fileName = substr($file, strrpos($file, '/') + 1);
                $contentDisposition = HeaderUtils::getContentDispositionHeader($fileName, \Yii::$app->request->userAgent);
                header($contentDisposition);
                flush();
                readfile($file);
                exit();
            }
        }
        echo '等待定时任务导出数据';
    }
}
