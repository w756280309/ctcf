<?php
namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineRepaymentRecord;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use yii\data\Pagination;

class IssuerController extends BaseController
{
    /**
     * 立合旺通统计列表.
     * 1. 每页显示20条记录;
     */
    public function actionLhList()
    {
        $record = $this->getRecord(1, 20);

        return $this->render('list', $record);
    }

    /**
     * 发行方统计数据导出.
     * 当面没有验证登录者身份,此处优化后期等融资方较多时,再行优化;
     */
    public function actionExport($id)
    {
        $record = $this->getRecord($id);

        header("Content-Type: application/vnd.ms-excel");
        $file_name = $record['issuer']->name . "-" . date('Ymd') . ".xls";

        $encoded_filename = str_replace("+", "%20", urlencode($file_name));
        $ua = $_SERVER["HTTP_USER_AGENT"];

        if (preg_match("/MSIE/", $ua) || preg_match("/Trident\/7.0/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else if (preg_match("/Firefox/", $ua)) {
            header('Content-Disposition: attachment; filename*="utf8\'\'' . $file_name . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $file_name . '"');
        }

        header("Pragma: no-cache");
        header("Expires: 0");

        $this->layout = false;
        echo $this->render('export', $record);
    }

    private function getRecord($id = null, $pageNum = null)
    {
        $issuer = $this->findOr404(Issuer::class, ['id' => $id]);

        $query = OnlineProduct::find()
            ->where(['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'issuer' => $issuer->id])
            ->orderBy(['created_at' => SORT_DESC]);

        if (empty($pageNum)) {
            $model = $query->innerJoinWith('borrower')->all();
        } else {
            $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => $pageNum]);
            $model = $query->offset($pages->offset)->limit($pages->limit)->all();
        }

        $plan = [];
        $refundTime = [];
        foreach ($model as $key => $val) {
            if (in_array($val->status, [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_OVER])) {
                $plan[$key] = OnlineRepaymentPlan::find()
                    ->where(['online_pid' => $val->id])
                    ->groupBy('online_pid, qishu')
                    ->select(['totalBenjin' => 'sum(benjin)', 'totalLixi' => 'sum(lixi)', 'refund_time', 'qishu', 'online_pid', 'count' => 'count(*)'])
                    ->asArray()
                    ->all();

                foreach ($plan[$key] as $v) {
                    $data = OnlineRepaymentRecord::find()
                        ->where(['online_pid' => $val->id, 'qishu' => $v['qishu']])
                        ->orderBy('refund_time desc')
                        ->all();

                    if ((int) $v['count'] !== count($data)) {       //每期实际放款时间以当期还清状态下的最后一笔为准,总的实际还款日期以全部还款成功的最后一笔为准
                        break;
                    } else {
                        $refundTime[$key][$v['qishu']] = $data[0]->refund_time;
                    }
                }
            }
        }

        return ['issuer' => $issuer, 'model' => $model, 'plan' => $plan, 'refundTime' => $refundTime, 'pages' => $pages];
    }
}
