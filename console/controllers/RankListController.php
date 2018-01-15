<?php
namespace console\controllers;
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 18-1-11
 * Time: 下午3:56
 */
use common\utils\SecurityUtils;
use yii\console\Controller;
use Yii;

/**
 * Class RankListController
 * @package console\controllers
 * 统计一段时间内用户累计投资年化的排行榜
 */
class RankListController extends Controller
{
    public $startDate = null; //开始时间(包含临界值)
    public $endDate = null;   //结束时间(包含临界值)
    public $num = null;            //数量（排行榜展示多少条）

    public function actionIndex()
    {
        $this->startDate = Yii::$app->params['ranking-list']['startDate'];
        $this->endDate = Yii::$app->params['ranking-list']['endDate'];
        $this->num = Yii::$app->params['ranking-list']['num'];
        //统计到活动结束时间的10分钟后
        $now = time();
        $end = strtotime($this->endDate);
        if ($now <= bcadd($end, 10 * 60 + 24 * 3600)) {
            $redis = Yii::$app->redis;
            //最后一个订单id，无变化则不重新计算
            $lastOrderId = \Yii::$app->db->createCommand(
                "select max(id) from online_order"
            )->queryScalar();
            if ($lastOrderId != $redis->get('last-order-id')) {
                $db = \Yii::$app->db;
                $sql = "
select 
    sum(truncate((if(p.refund_method > 1, o.order_money*p.expires/12, o.order_money*p.expires/365)), 2)) as annual, 
    u.safeMobile, 
    max(o.id) as oid 
from online_order o 
inner join online_product p 
    on o.online_pid = p.id 
inner join user u 
    on u.id = o.uid 
where date(from_unixtime(o.order_time)) >= :startDate 
    and date(from_unixtime(o.order_time)) <= :endDate 
    and o.status = 1
group by u.id 
order by annual desc, 
    oid asc 
limit :num";
                $res = $db->createCommand($sql, [
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                    'num' => $this->num,
                ])->queryAll();
                $arr = [];
                foreach ($res as $k => $v) {
                    $arr[] = ['mobile' => SecurityUtils::decrypt($v['safeMobile']), 'annual' => $v['annual']];
                }
                $redis->set('ranking-list', json_encode($arr));
                $redis->set('last-order-id', $lastOrderId);
            }
        }
    }

    public function actionDel()
    {
        $redis = Yii::$app->redis;
        $redis->del('ranking-list');
        $redis->del('last-order-id');
    }
}
