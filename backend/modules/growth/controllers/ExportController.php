<?php

namespace backend\modules\growth\controllers;


use backend\controllers\BaseController;
use common\utils\TxUtils;
use console\command\SqlExportJob;
use common\utils\SecurityUtils;

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
                'sql' => "",//导出的sql模板, 使用预处理方式调用, 不可为空
                'params' => [//如果没有必要参数, 可以为null, 但是必须是isset
                    'repaymentDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'repaymentDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d'),//参数的默认值
                        'title' => '回款日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['姓名', '手机号', '年龄', '分销商', '投资金额', '利率', '标的名称', '还款方式', '标的状态', '标的截止日期', '还款本金', '还款利息', '还款本息', '实际还款时间', '原计划还款时间', '可用余额'],//统计的数据项，不可为空
                'itemType' => ['string', 'int', 'int', 'string', 'float', 'float', 'string', 'string', 'string', 'string','float', 'float', 'float', 'date', 'date', 'float'],
                'beforeExport' => function($row) {
                    $row['手机号'] = SecurityUtils::decrypt($row['手机号']);
                    $row['年龄'] = date('Y') - substr(SecurityUtils::decrypt($row['年龄']), 6, 4);
                    return $row;
                }
            ],
            'last_ten_day_draw' => [
                'key' => 'last_ten_day_draw',
                'title' => '最近10天成功提现数据',
                'content' => '统计最近10天每天每个用户的累计成功提现金额, 时间以发起提现时间为准',
                'sql' => "SELECT u.safeMobile AS  '手机号', u.real_name AS  '姓名', SUM( d.money ) AS  '累计成功提现金额', DATE( FROM_UNIXTIME( d.created_at ) ) AS  '提现发起日期', 
ui.lastInvestDate AS '未投资时长',
ua.available_balance AS '可用余额'
FROM draw_record AS d
INNER JOIN user AS u ON u.id = d.uid
INNER JOIN user_info AS ui ON ui.user_id = d.uid
INNER JOIN user_account AS ua ON ua.uid = d.uid
WHERE d.status =2
AND u.type =1
AND FROM_UNIXTIME( d.created_at ) >= date_sub(curdate(), INTERVAL 10 DAY)
GROUP BY d.uid, DATE( FROM_UNIXTIME( d.created_at ) ) 
ORDER BY DATE( FROM_UNIXTIME( d.created_at ) ) DESC , d.uid ASC ",
                'params' => null,
                'itemLabels' => ['手机号', '姓名', '累计成功提现金额', '提现发起日期', '未投资时长', '可用余额'],
                'itemType' => ['int', 'string', 'float', 'date', 'int', 'float'],
                'beforeExport' => function($row) {
                    $row['手机号'] = SecurityUtils::decrypt($row['手机号']);
                    return $row;
                }
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
            'custormer_annual_invest' => [
                'key' => 'custormer_annual_invest',
                'title' => '指定日期累计年化',
                'content' => '指定日期累计年化',
                'sql' => "SELECT 
u.real_name 姓名, 
u.safeMobile 手机号,
sum(truncate((if(p.refund_method > 1, o.order_money*p.expires/12, o.order_money*p.expires/365)), 2)) as 累计年化金额 
from online_order o 
inner join online_product p on o.online_pid = p.id 
inner join user u on u.id = o.uid 
where date(from_unixtime(o.order_time)) >= :startDate 
and date(from_unixtime(o.order_time)) <= :endDate 
and o.status = 1 
group by o.uid 
order by 累计年化金额 desc;",
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
                'itemLabels' => ['姓名', '手机号', '累计年化金额'],
                'itemType' => ['string', 'string', 'string'],
            ],
            'order_no_licai_plan' => [
                'key' => 'order_no_licai_plan',
                'title' => '贷后余额用户投资明细',
                'content' => '贷后余额用户投资明细（不含理财计划）',
                'sql' => 'select 
p.title 标的名称,
p.sn 标的sn,
o.yield_rate 实际购买利率,
o.username 购买人姓名,
o.order_money 购买金额,
u.safeMobile 手机号,
u.safeIdCard 身份证号
from online_order o 
inner join online_product p on o.online_pid = p.id
inner join user u on u.id = o.uid
where p.status in (5,6,7) 
and o.status = 1 
and (p.isLicai= 0 or isnull(p.isLicai))
and p.funded_money > 0 
and p.isTest = 0
order by p.id asc',
            'params' => null,
            'itemLabels' => ['标的名称', '标的sn', '实际购买利率', '购买人姓名', '购买金额', '手机号', '身份证号'],
            'itemType' => ['string', 'string', 'string', 'string', 'string', 'string', 'string'],
            ],
            'export_referral_user_info' => [
                'key' => 'export_referral_user_info',
                'title' => '渠道用户信息导出',
                'content' => '渠道用户信息导出',
                'sql' => 'select 
from_unixtime(u.created_at) 注册时间,u.campaign_source 渠道码,u.real_name 姓名,u.safeMobile 手机号,if(ub.id>0, 1, 0) 是否绑卡,ui.investTotal 投资总金额
from user u
left join user_info ui on ui.user_id = u.id
left join user_bank ub on ub.uid = u.id
where u.campaign_source in (:campaignSource)
and date(from_unixtime(u.created_at)) >= :startDate
and date(from_unixtime(u.created_at)) <= :endDate',
                'params' => [
                    'startDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'startDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('-2 day')),//参数的默认值
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
                    'campaignSource' => [//参数列表， key 是参数名， 不可为空
                        'name' => '渠道信息',//参数名
                        'type' => 'string',//参数的数据类型
                        'value' => '',//参数的默认值
                        'title' => '渠道码',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['注册时间', '渠道码', '姓名', '手机号', '是否绑卡', '投资总金额'],
                'itemType' => ['date', 'string', 'string', 'string', 'int', 'string'],
            ],
            'export_expires_and_invest' => [
                'key' => 'export_expires_and_invest',
                'title' => '投资指定产品期限并达指定金额',
                'content' => '投资指定产品期限并达指定金额',
                'sql' => 'select 
u.real_name 姓名, u.safeMobile 手机号, ui.investTotal 累计交易金额, ui.lastInvestDate 最后一次投资日期 
from user_info ui
inner join user u on ui.user_id = u.id
where ui.investTotal >= :money
and ui.user_id not in (
select distinct(o.uid) from online_order o 
inner join online_product p on o.online_pid = p.id 
where o.status = 1 
and ((p.refund_method = 1 and p.expires >= :days) or (p.refund_method > 1 and p.expires >= :months)) 
and p.isTest = 0)',
                'params' => [
                    'money' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'money',//参数名
                        'type' => 'number',//参数的数据类型
                        'value' => '200000',//参数的默认值
                        'title' => '投资金额',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                    'days' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'days',//参数名
                        'type' => 'number',//参数的数据类型
                        'value' => 365,//参数的默认值
                        'title' => '到期产品期限（天数）',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                    'months' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'months',//参数名
                        'type' => 'number',//参数的数据类型
                        'value' => 12,//参数的默认值
                        'title' => '非到期产品期限（月份）',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['姓名', '手机号', '累计交易金额', '最后一次投资日期'],
                'itemType' => ['string', 'string', 'string', 'date'],
            ],
            'xs_due_list_export' => [
                'key' => 'xs_due_list_export',
                'title' => '新手标数据导出',
                'content' => '导出新手标数据信息',
                'sql' => 'select 
u.real_name 姓名,
u.safeMobile 手机号,
from_unixtime(p.finish_date) 到期时间,
o.benxi 到期金额,a.name 分销商
from online_repayment_record o 
inner join online_product p on o.online_pid = p.id 
inner join user u on u.id = o.uid 
left join user_affiliation ua on ua.user_id = o.uid 
left join affiliator a on a.id = ua.affiliator_id 
where date(from_unixtime(p.finish_date)) >= :startDate 
and date(from_unixtime(p.finish_date)) <= :endDate 
and o.status in (1,2) 
and p.is_xs = 1 
and p.isTest = 0 
order by p.finish_date asc',
                'params' => [
                    'startDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'startDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('-2 day')),//参数的默认值
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
                'itemLabels' => ['姓名', '手机号', '到期时间', '到期金额', '分销商'],
                'itemType' => ['string', 'string', 'date', 'string', 'string'],
            ],
            'export_referral_user_count' => [
                'key' => 'export_referral_user_count',
                'title' => '渠道用户统计信息导出',
                'content' => '渠道用户统计信息导出',
                'sql' => 'select 
u.campaign_source 分销渠道,
count(distinct(u.id)) 注册人数, 
count(distinct(o.uid)) 购买人数, 
sum(o.order_money) 购买金额 
from user u 
left join online_order o on u.id = o.uid 
where u.campaign_source in (:campaignSource) 
and date(from_unixtime(u.created_at)) >= :startDate 
and date(from_unixtime(u.created_at)) <= :endDate 
group by u.campaign_source',
                'params' => [
                    'startDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'startDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('-2 day')),//参数的默认值
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
                    'campaignSource' => [//参数列表， key 是参数名， 不可为空
                        'name' => '渠道信息',//参数名
                        'type' => 'string',//参数的数据类型
                        'value' => '',//参数的默认值
                        'title' => '渠道码',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['分销渠道', '注册人数', '购买人数', '购买金额'],
                'itemType' => ['string', 'string', 'string', 'string'],
            ],
            'export_nbxdjb_finish' => [
                'key' => 'export_nbxdjb_finish',
                'title' => '现代金报客户到期名单导出',
                'content' => '现代金报客户到期名单导出',
                'sql' => 'select 
u.real_name 姓名,
u.safeMobile 联系方式,
p.title 产品名称,
p.expires 产品期限,
p.refund_method 单位,
p.finish_date 到期日,
pl.benxi 到期金额
from online_repayment_plan pl
inner join user u on pl.uid = u.id 
inner join online_product p on pl.online_pid = p.id
inner join (select online_pid,max(qishu) mq from online_repayment_plan group by online_pid) d on d.online_pid = pl.online_pid 
inner join user_affiliation as a on a.user_id = u.id
where d.mq = pl.qishu
and a.trackCode = \'nbxdjb\'
and date(from_unixtime(p.finish_date)) >= :startDate 
and date(from_unixtime(p.finish_date)) <= :endDate',
                'params' => [
                    'startDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'startDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d'),//参数的默认值
                        'title' => '开始日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                    'endDate' => [//参数列表， key 是参数名， 不可为空
                        'name' => 'endDate',//参数名
                        'type' => 'date',//参数的数据类型
                        'value' => date('Y-m-d', strtotime('+30 day')),//参数的默认值
                        'title' => '结束日期',//参数标题
                        'isRequired' => true,//是否必要参数, 默认都是必要参数
                    ],
                ],
                'itemLabels' => ['姓名', '联系方式', '产品名称', '产品期限', '单位', '到期日', '到期金额'],
                'itemType' => ['string', 'string', 'string', 'string', 'string', 'date', 'string'],
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
                    'key' => $key,
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
