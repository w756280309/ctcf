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
        if ( is_dir($path)) {
            $handle = opendir( $path );
            if ($handle) {
                while ( false !== ( $item = readdir( $handle ) ) ) {
                    if ( $item != "." && $item != ".." ) {
                        if (false !== strpos($item, '立合旺通') ) {
                            $fileName = $path . '/'. $item;
                            header(HeaderUtils::getContentDispositionHeader($item, \Yii::$app->request->userAgent));
                            readfile($fileName);
                            exit();
                        }
                    }
                }
            }
            closedir( $handle );
        }
        echo '等待定时任务导出数据';
        exit;
    }
}
