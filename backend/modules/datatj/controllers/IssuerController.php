<?php
namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\models\product\Issuer;
use Wcg\Http\HeaderUtils;

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
        $path  = rtrim(\Yii::$app->params['backend_tmp_share_path'], '/');
        $fileName = 'lihewangtong.xlsx';
        $file = $path . '/'.$fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->xSendFile('/downloads/' . $fileName, $fileName, [
                'xHeader' => 'X-Accel-Redirect',
            ]);
        }
        echo '等待定时任务导出数据';
        exit;
    }
}
