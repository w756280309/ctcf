<?php

namespace api\modules\v1\controllers;

use common\lib\user\UserStats;
use common\models\LenderStats;

/**
 * 投资用户统计数据,包括(ID，注册时间，充值金额&次数，取现金额&次数，投资金额&次数，是否开户，是否绑卡，客户姓名，联系方式（即手机号码），身份证号，账户余额).
 */
class LenderStatsController extends Controller
{
    /**
     * 导出数据（原）
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionExport()
    {
        $data = UserStats::collectLenderData();
        UserStats::createCsvFile($data);
    }

    /**
     * 导出数据（新）
     */
    public function actionNewExport()
    {
        LenderStats::createCsvFile();
    }
}