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
            $db = \Yii::$app->db;
            $sql = "
select 
    sum(truncate((if(p.refund_method > 1, o.order_money*p.expires/12, o.order_money*p.expires/365)), 2)) as annual, 
    u.safeMobile,
    u.offlineUserId, 
    max(o.id) as oid 
from online_order o 
inner join online_product p 
    on o.online_pid = p.id 
inner join user u 
    on u.id = o.uid 
where date(from_unixtime(o.order_time)) >= :startDate 
    and date(from_unixtime(o.order_time)) <= :endDate 
    and o.status = 1
    and u.safeMobile is not null
group by u.id 
";
            $res = $db->createCommand($sql, [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ])->queryAll();

            $sqlOffline = "
select 
    sum(truncate((if(p.repaymentMethod > 1, o.money*p.expires*10000/12, o.money*p.expires*10000/365)), 2)) as annual, 
    u.id,
    u.mobile,
    max(o.id) as offid
from offline_order o 
inner join offline_loan p 
    on o.loan_id = p.id 
inner join offline_user u 
    on u.id = o.user_id 
where date(o.orderDate) >= :startDate
    and date(o.orderDate) <= :endDate 
    and o.isDeleted = 0
group by u.id
";
            $resOffline = $db->createCommand($sqlOffline, [
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
            ])->queryAll();
            //整理数据
            foreach ($res as $k => $v) {
                foreach ($resOffline as $m => $n) {
                    if($v['offlineUserId'] == $n['id']) {
                        $res[$k]['annual'] += $n['annual'];
                        unset($resOffline[$m]);
                    }
                }
            }
            $res = array_merge($res, $resOffline);
            uasort($res, function($m ,$n) {
                if ($m['annual'] > $n['annual']) {
                    return 1;
                } else if ($m['annual'] == $n['annual']) {
                    if (isset($m['oid'])) {
                        if (isset($n['oid'])) {
                            if($m['oid'] < $n['oid']) {
                                return 1;
                            } else {
                                return -1;
                            }
                        } else {
                            return 1;
                        }
                    } else {
                        if (isset($n['oid'])) {
                            return -1;
                        } else {
                            if ($m['offid'] > $n['offid']) {
                                return -1;
                            } else {
                                return 1;
                            }
                        }
                    }

                } else {
                    return -1;
                }
            });
            //获取规定的条数
            $data = [];
            $num = $this->num;
            if (count($res) < $num) {
                $num = count($res);
            }
            for ($i = 0; $i < $num; $i ++) {
                $data[] = array_pop($res);
            }
            $arr = [];
            foreach ($data as $k => $v) {
                $arr[] = ['mobile' => isset($v['safeMobile']) ? SecurityUtils::decrypt($v['safeMobile']) : $v['mobile'], 'annual' => $v['annual']];
            }
            $redis->set('ranking-list', json_encode($arr));
        }
    }

    public function actionDel()
    {
        $redis = Yii::$app->redis;
        $redis->del('ranking-list');
        $redis->del('last-order-id');
    }

    public function actionGet()
    {
        $redis = Yii::$app->redis;
        var_dump($redis->get('ranking-list'));
    }
}
