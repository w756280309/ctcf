<?php

namespace common\service;

use Yii;
use common\models\product\OnlineProduct;
use yii\data\Pagination;

/**
 * Desc 主要用于订单获取
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class OrderService
{
    private $pz = 5;//每页尺寸
    public function __construct()
    {
    }

    /**
     * 获取用户订单列表.
     *
     * @param type $uid
     *
     * @return bool
     */
    public function getUserOrderList($uid = null, $type = null, $page = 1)
    {
        if (empty($uid)) {
            return false;
        }
        $loan = new LoanService();
        $query1 = (new \yii\db\Query())
                ->select('order.*,p.title,p.status pstatus,p.end_date penddate,p.expires expiress,p.finish_date,p.jiaxi')
                ->from(['online_order order'])
                ->innerJoin('online_product p', 'order.online_pid=p.id')
                ->where(['order.uid' => $uid, 'order.status' => 1]);

        if (!empty($type)) {
            $query1->andWhere(['p.status' => $type]);
        }

        $querysql = $query1->orderBy('order.id desc')->createCommand()->getRawSql();
        $query = (new \yii\db\Query())
                ->select('*')
                ->from(['('.$querysql.')T']);
        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $this->pz]);
        $query = $query->offset(($page - 1) * ($this->pz))->limit($pages->limit)->all();
        $tp = ceil($count / $this->pz);
        $header = [
            'count' => intval($count),
            'size' => $this->pz,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';
        foreach ($query as $key => $dat) {
            $query[$key]['statusval'] = Yii::$app->params['deal_status'][$dat['pstatus']];
            $query[$key]['order_time'] = $dat['order_time'] ? date('Y-m-d', $dat['order_time']) : '';
            $query[$key]['jiaxi'] = $dat['jiaxi'];
            if (in_array($dat['pstatus'], [OnlineProduct::STATUS_NOW])) {
                $query[$key]['profit'] = '--';
                $query[$key]['returndate'] = date('Y-m-d', $dat['finish_date']);
            } elseif (in_array($dat['pstatus'], [OnlineProduct::STATUS_HUAN, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND])) {
                //
                $replayment = \common\models\order\OnlineRepaymentPlan::findOne(['order_id' => $dat['id'], 'online_pid' => $dat['online_pid']]);
                if (null === $replayment) {
                    $query[$key]['profit'] = '--';
                } else {
                    $query[$key]['profit'] = $replayment->lixi;
                }
                $query[$key]['returndate'] = date('Y-m-d', $dat['finish_date']);
            } else {
                $replayment = \common\models\order\OnlineRepaymentRecord::findOne(['order_id' => $dat['id'], 'online_pid' => $dat['online_pid']]);
                $query[$key]['profit'] = $replayment->lixi;
                $query[$key]['returndate'] = date('Y-m-d', $replayment->refund_time);
            }
        }

        return ['header' => $header, 'data' => $query, 'code' => $code, 'message' => $message];
    }
}
