<?php

namespace backend\modules\growth\controllers;


use backend\controllers\BaseController;
use common\utils\TxUtils;
use console\command\SqlExportJob;

//根据sql导出excel
class ExportController extends BaseController
{
    private $exportConfig;

    public function init()
    {
        $this->layout = '@backend/views/layouts/frame.php';
        //todo 功能及需求稳定之后，可以使用数据库存储配置信息
        $this->exportConfig = [
            'repayment_expire_interest' => [
                'key' => 'repayment_expire_interest',//每个导出类型的唯一标示, 不可为空
                'title' => '指定日期还款数据',//导出类型的标题，不可为空
                'content' => '统计指定日期回款数据',//导出类型的说明，不可为空
                'sql' => "SELECT u.real_name AS '姓名',
u.mobile AS '手机号',
year(now()) - substring(u.idCard,7,4) as '年龄',
a.name as '分销商',
o.order_money AS '投资金额',
o.yield_rate AS '利率',
p.title AS '标的标题',
CASE p.refund_method
WHEN 1
THEN  '到期本息'
WHEN 2
THEN  '按月付息，到期本息'
WHEN 3
THEN  '按季付息，到期本息'
WHEN 4
THEN  '按半年付息，到期本息'
WHEN 5
THEN  '按年付息，到期本息'
WHEN 6
THEN  '按自然月付息，到期本息'
WHEN 7
THEN  '按自然季度付息，到期本息'
WHEN 8
THEN  '按自然半年付息，到期本息'
WHEN 9
THEN  '按自然年付息，到期本息'
WHEN 10
THEN  '等额本息'
END AS  '还款方式',
if(p.status = 5, '还款中', '已还清') as '标的状态',
date(from_unixtime(p.finish_date)) as '标的截止日期',
 r.benjin AS '还款本金',
 r.lixi AS '还款利息',
 r.benxi AS '还款本息',
DATE(r.`actualRefundTime`) AS '实际还款时间'
FROM `online_repayment_plan` AS r
INNER JOIN user AS u ON r.uid = u.id
INNER JOIN online_product AS p ON p.id = r.online_pid
INNER JOIN online_order AS o ON o.id = r.order_id
left join user_affiliation as ua on ua.user_id = u.id
left join affiliator as a on a.id = ua.affiliator_id
WHERE u.type =1
AND p.isTest = false
AND p.status
IN ( 5, 6 ) 
AND r.status
IN ( 1, 2 ) 
AND date(r.`actualRefundTime`) = :repaymentDate
AND o.status =1
ORDER BY p.id asc ,r.uid asc",//导出的sql模板, 使用预处理方式调用, 不可为空
                'params' => [//如果没有必要参数, 可以为null, 但是必须是isset
                    'repaymentDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'repaymentDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d'),//参数的默认值
                        'title' => '回款日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['姓名', '手机号', '年龄', '分销商', '投资金额', '利率', '标的名称', '还款方式', '标的状态', '标的截止日期', '还款本金', '还款利息', '还款本息', '实际还款时间'],//统计的数据项，不可为空
                'itemType' => ['string', 'int', 'int', 'string', 'float', 'float', 'string', 'string', 'string', 'string','float', 'float', 'float', 'date'],
            ],
            'last_ten_day_draw' => [
                'key' => 'last_ten_day_draw',
                'title' => '最近10天成功提现数据',
                'content' => '统计最近10天每天每个用户的累计成功提现金额, 时间以发起提现时间为准',
                'sql' => "SELECT u.mobile AS  '手机号', u.real_name AS  '姓名', SUM( d.money ) AS  '累计成功提现金额', DATE( FROM_UNIXTIME( d.created_at ) ) AS  '提现发起日期'
FROM draw_record AS d
INNER JOIN user AS u ON u.id = d.uid
WHERE d.status =2
AND u.type =1
AND FROM_UNIXTIME( d.created_at ) >= date_sub(curdate(), INTERVAL 10 DAY)
GROUP BY d.uid, DATE( FROM_UNIXTIME( d.created_at ) ) 
ORDER BY DATE( FROM_UNIXTIME( d.created_at ) ) DESC , d.uid ASC ",
                'params' => null,
                'itemLabels' => ['手机号', '姓名', '累计成功提现金额', '提现发起日期'],
                'itemType' => ['int', 'string', 'float', 'date'],
            ],
            'stats_custormer_service_bound' => [
                'key' => 'stats_custormer_service_bound',
                'title' => '客服呼入呼出量',
                'content' => '统计客服呼入呼出量',
                'sql' => "select ad.real_name AS '客服姓名', SUM(if(pc.direction='inbound', 1, 0)) AS '呼入量', SUM(if(pc.direction='outbound', 1, 0)) AS '呼出量' 
from crm_phone_call AS pc 
left join admin AS ad on pc.recp_id = ad.id 
where
    date(pc.createTime) <= :endDate
    and date(pc.createTime) >= :startDate
GROUP BY pc.recp_id",
                'params' => [//如果没有必要参数, 可以为null, 但是必须是isset
                    'startDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'startDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('-1 month')),//参数的默认值
                        'title' => '开始日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                    'endDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'endDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('-1 day')),//参数的默认值
                        'title' => '结束日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['客服姓名', '呼入量', '呼出量'],
                'itemType' => ['string', 'int', 'int'],
            ],

            'current_custormer_assets_million' => [
                'key' => 'current_custormer_assets_million',
                'title' => '资产大于100万的线下用户',
                'content' => '统计当前持有资产大于100万的线下用户',
                'sql' => "SELECT 
u.realName AS 姓名,
u.mobile AS 手机号,
IF( SUBSTR( u.idCard, -2, 1 ) %2,  '男',  '女' ) AS 性别,
( DATE_FORMAT( NOW( ) ,  '%Y' ) - SUBSTRING( u.idCard, 7, 4 ) ) AS 年龄,
sum(o.money * 10000) AS '持有资产'
from offline_order as o
inner join offline_user as u on o.user_id = u.id
inner join offline_loan as p on o.loan_id = p.id
where o.isDeleted = 0
and now() < date(p.finish_date)
group by o.user_id
having 持有资产 >= 1000000
order by 持有资产 desc",
                'params' => null,
                'itemLabels' => ['姓名', '手机号', '性别','年龄','持有资产'],
                'itemType' => ['string', 'string', 'string','int','float'],
            ],
        ];
        parent::init();
    }

    //导出类型选择页面
    public function actionIndex()
    {
        $exportConfig = $this->exportConfig;

        return $this->render('index', [
            'exportConfig' => $exportConfig,
        ]);

    }

    //申请导出页面
    public function actionConfirm($key)
    {
        if (!isset($this->exportConfig[$key])) {
            throw new \Exception('数据未找到');
        }

        $exportModel = $this->exportConfig[$key];
        if (
            empty($exportModel['params'])
            || \Yii::$app->request->isPost
        ) {
            $safeParams = [];
            $isVerified = true;
            if (!empty($exportModel['params'])) {
                foreach (\Yii::$app->request->post() as $param => $value) {
                    if (isset($exportModel['params'][$param])) {
                        if ($exportModel['params'][$param]['isRequired'] && empty($value)) {
                            $isVerified = false;
                            //todo 增加其他验证条件
                        } else {
                            $safeParams[$param] = $value;
                        }
                    }
                }
            }
            if ($isVerified) {
                //todo 可以增加下载记录
                $sn = TxUtils::generateSn('Export');
                $job = new SqlExportJob([
                    'sql' => $exportModel['sql'],
                    'queryParams' => $safeParams,
                    'exportSn' => $sn,
                    'itemLabels' => $exportModel['itemLabels'],
                    'itemType' => $exportModel['itemType'],
                ]);
                if (\Yii::$container->get('db_queue')->pub($job)) {
                    return $this->redirect('/growth/export/result?sn='.$sn.'&key='.$key);
                }
            }
        }

        return $this->render('confirm', [
            'exportModel' => $exportModel,
        ]);
    }

    //导出等待页面
    public function actionResult($sn, $key = '', $title = '')
    {
        $path = rtrim(\Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/' . $sn . '.xlsx';//todo 暂时不做下载sn和对应文件名的关联
        if (!empty($key)) {
            $exportModel = $this->exportConfig[$key];
            $title = $exportModel['title'];
        }
        $fileExists = file_exists($file);
        if (\Yii::$app->request->isAjax) {
            return [
                'fileExists' => $fileExists,
                'file' => $file,
            ];
        }

        return $this->render('result', [
            'sn' => $sn,
            'fileExists' => $fileExists,
            'title' => $title,
        ]);
    }

    //导出文件下载页面
    public function actionDownload($sn)
    {
        $path = rtrim(\Yii::$app->params['backend_tmp_share_path'], '/');
        $fileName = $sn . '.xlsx';//todo 暂时不做下载sn和对应文件名的关联
        $file = $path . '/' .$fileName;

        if (file_exists($file)) {
            return \Yii::$app->response->xSendFile('/downloads/' . $fileName, $fileName, [
                'xHeader' => 'X-Accel-Redirect',
            ]);
        } else {
            throw new \Exception('文件未找到');
        }
    }
}