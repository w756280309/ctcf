<?php

namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\lib\user\UserStats;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\stats\Perf;
use common\models\stats\Piwik;
use common\models\user\User;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class DatatjController extends BaseController
{
    /**
     * 汇总统计页面
     */
    public function actionHuizongtj()
    {
        $data = [];
        $redis = Yii::$app->redis;
        $datatj = $redis->get('datatj.actionHuizongtj');

        if (null !== $datatj) {
            $data = json_decode($datatj, true);
        }

        return $this->render('huizongtj', $data);
    }

    /**
     * 月统计页面
     */
    public function actionMonthtj()
    {
        $redis = Yii::$app->redis;
        $historyMonthData = [];
        $monthData = [];
        $monthInvestor = [];
        $lastUpdateTime = null;
        if ($redis->hexists('datatj.actionMonthtj', 'historyMonthData')
            && $redis->hexists('datatj.actionMonthtj', 'monthData')
            && $redis->hexists('datatj.actionMonthtj', 'monthInvestor')
        ) {
            $historyMonthData = json_decode($redis->hget('datatj.actionMonthtj', 'historyMonthData'), true);
            $monthData = json_decode($redis->hget('datatj.actionMonthtj', 'monthData'), true);
            $monthInvestor = json_decode($redis->hget('datatj.actionMonthtj', 'monthInvestor'), true);
            $lastUpdateTime = $redis->hget('datatj.actionMonthtj', 'lastUpdateTime');
        }

        $allData = array_merge([$monthData], $historyMonthData);
        $pages = new Pagination(['totalCount' => count($allData), 'pageSize' => 20]);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);

        $fileData = [
            'pages' => $pages,
            'dataProvider' => $dataProvider,
            'monthInvestor' => $monthInvestor,
            'lastUpdateTime' => $lastUpdateTime,
        ];

        return $this->render('monthtj', $fileData);
    }

    /**
     * 日统计页面
     */
    public function actionDaytj()
    {
        $redis = Yii::$app->redis;
        $allData = [];
        $todayData = [];
        $lastUpdateTime = null;
        if ($redis->hexists('datatj.actionDaytj', 'todayData')) {
            $todayData = json_decode($redis->hget('datatj.actionDaytj', 'todayData'), true);
            $lastUpdateTime = $redis->hget('datatj.actionDaytj', 'lastUpdateTime');
        }

        $sql = "SELECT * FROM perf WHERE bizDate < DATE_FORMAT(NOW(),'%Y-%m-%d') order by bizDate desc";
        $allData = Yii::$app->db_read->createCommand($sql)->queryAll();
        $allData = array_merge([$todayData], $allData);
        $pages = new Pagination(['totalCount' => count($allData), 'pageSize' => 20]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);
        $fileData = [
            'pages' => $pages,
            'dataProvider' => $dataProvider,
            'lastUpdateTime' => $lastUpdateTime,
        ];

        return $this->render('daytj', $fileData);
    }

    //日历史数据导出
    public function actionDayExport()
    {
        //获取历史数据
        $history = Perf::find()->where(['<', 'bizDate', date('Y-m-d')])->orderBy(['bizDate' => SORT_DESC])->asArray()->all();
        $today = Perf::getTodayCount();
        $allData = array_merge([$today], $history);
        $record = implode(',', [
                '日期',
                '交易总额',
                '线上交易额',
                '线下交易额',
                '充值金额',
                '提现金额',
                '充值手续费',
                Yii::$app->params['pc_cat'][2] . '销售额',
                Yii::$app->params['pc_cat'][1] . '销售额',
                '注册用户',
                '实名认证',
                '绑卡用户数',
                '投资人数',
                '当日注册当日投资人数',
                '新增投资人数',
                '当日注册当日投资金额',
                '非当日注册当日投资金额',
                '已投用户登录数',
                '未投用户登录数',
                '签到用户数',
                '融资项目',
                '理财计划新增投资人数',
                '理财计划新增投资用户的投资金额',
                '理财计划总投资人数',
                '理财计划总投资金额',
                '新手标新增投资人数',
                '新手标新增用户的投资金额',
                '新手标总投资人数',
                '新手标总投资金额',
                '实际回款金额',
                '实际回款项目数',
                '实际回款人数',
            ]) . "\n";
        foreach ($allData as $k => $data) {
            $array = [
                $data['bizDate'],
                floatval($data['totalInvestment']),
                floatval($data['onlineInvestment']),
                floatval($data['offlineInvestment']),
                floatval($data['rechargeMoney']),
                floatval($data['drawAmount']),
                floatval($data['rechargeCost']),
                floatval($data['investmentInWyb']),
                floatval($data['investmentInWyj']),
                intval($data['reg']),
                intval($data['idVerified']),
                intval($data['qpayEnabled']),
                intval($data['investor']),
                intval($data['newRegisterAndInvestor']),
                intval($data['newInvestor']),
                floatval($data['newRegAndNewInveAmount']),
                floatval($data['preRegAndNewInveAmount']),
                intval($data['investAndLogin']),
                intval($data['notInvestAndLogin']),
                intval($data['checkIn']),
                intval($data['successFound']),
                intval($data['licaiNewInvCount']),
                floatval($data['licaiNewInvSum']),
                intval($data['licaiInvCount']),
                floatval($data['licaiInvSum']),
                intval($data['xsNewInvCount']),
                floatval($data['xsNewInvSum']),
                intval($data['xsInvCount']),
                floatval($data['xsInvSum']),
                floatval($data['repayMoney']),
                intval($data['repayLoanCount']),
                intval($data['repayUserCount']),
            ];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="day-count(' . date('Y-m-d') . ').csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    //月数据导出
    public function actionMonthExport()
    {
        //每月回款人数
        $repayData = Perf::getMonthRepayUserCount();
        $repayData = ArrayHelper::index($repayData, 'm');
        //获取当月数据
        $month = Perf::getThisMonthCount();
        //历史数据，不包含当月
        $sql = "SELECT DATE_FORMAT(bizDate,'%Y-%m') as bizDate,
SUM(totalInvestment) AS totalInvestment,
SUM(onlineInvestment) AS onlineInvestment,
SUM(offlineInvestment) AS offlineInvestment,
SUM(rechargeMoney) AS rechargeMoney,
SUM(drawAmount) AS drawAmount,
SUM(rechargeCost) AS rechargeCost,
SUM(reg) AS reg,
SUM(idVerified) AS idVerified,
SUM(successFound) AS successFound,
SUM(qpayEnabled) AS qpayEnabled,
SUM(investor) AS investor,
SUM(newRegisterAndInvestor) AS newRegisterAndInvestor,
SUM(newInvestor) AS newInvestor,
SUM(newRegAndNewInveAmount) AS newRegAndNewInveAmount,
SUM(preRegAndNewInveAmount) AS preRegAndNewInveAmount,
SUM(investmentInWyb) AS investmentInWyb,
SUM(investmentInWyj) AS investmentInWyj,
SUM(licaiNewInvCount) AS licaiNewInvCount,
SUM(licaiNewInvSum) AS licaiNewInvSum,
SUM(licaiInvCount) AS licaiInvCount,
SUM(licaiInvSum) AS licaiInvSum,
SUM(xsNewInvCount) AS xsNewInvCount,
SUM(xsNewInvSum) AS xsNewInvSum,
SUM(xsInvCount) AS xsInvCount,
SUM(xsInvSum) AS xsInvSum,
SUM(checkIn) as checkIn,
SUM(repayMoney) as repayMoney,
SUM(repayLoanCount) as repayLoanCount
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m') < DATE_FORMAT(NOW(),'%Y-%m')  GROUP BY DATE_FORMAT(bizDate,'%Y-%m') ORDER BY DATE_FORMAT(bizDate,'%Y-%m') DESC";
        $history = Yii::$app->db->createCommand($sql)->queryAll();
        $allData = array_merge([$month], $history);
        $record = implode(',', [
                '日期',
                '交易总额',
                '线上交易额',
                '线下交易额',
                '充值金额',
                '提现金额',
                '充值手续费',
                Yii::$app->params['pc_cat'][2] . '销售额',
                Yii::$app->params['pc_cat'][1] . '销售额',
                '注册用户',
                '实名认证',
                '签到人次',
                '绑卡用户数',
                '投资人数',
                '当日注册当日投资人数',
                '新增投资人数',
                '当日注册当日投资金额',
                '非当日注册当日投资金额',
                '融资项目',
                '理财计划新增投资人数',
                '理财计划新增投资用户的投资金额',
                '理财计划总投资人数',
                '理财计划总投资金额',
                '新手标新增投资人数',
                '新手标新增用户的投资金额',
                '新手标总投资人数',
                '新手标总投资金额',
                '实际回款金额',
                '实际回款项目数',
                '实际回款人数',
            ]) . "\n";
        foreach ($allData as $k => $data) {
            $array = [
                $data['bizDate'],
                floatval($data['totalInvestment']),
                floatval($data['onlineInvestment']),
                floatval($data['offlineInvestment']),
                floatval($data['rechargeMoney']),
                floatval($data['drawAmount']),
                floatval($data['rechargeCost']),
                floatval($data['investmentInWyb']),
                floatval($data['investmentInWyj']),
                intval($data['reg']),
                intval($data['idVerified']),
                intval($data['checkIn']),
                intval($data['qpayEnabled']),
                intval($data['investor']),
                intval($data['newRegisterAndInvestor']),
                intval($data['newInvestor']),
                floatval($data['newRegAndNewInveAmount']),
                floatval($data['preRegAndNewInveAmount']),
                intval($data['successFound']),
                intval($data['licaiNewInvCount']),
                floatval($data['licaiNewInvSum']),
                intval($data['licaiInvCount']),
                floatval($data['licaiInvSum']),
                intval($data['xsNewInvCount']),
                floatval($data['xsNewInvSum']),
                intval($data['xsInvCount']),
                floatval($data['xsInvSum']),
                floatval($data['repayMoney']),
                intval($data['repayLoanCount']),
                isset($repayData[$data['bizDate']]) ? $repayData[$data['bizDate']]['c'] : 0,
            ];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="month-count(' . date('Y-m') . ').csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    /**
     * 根据待筛选类型、数据项、日期获取筛选到的用户ID
     * @param string $type  筛选类型，day:日筛选，month:月筛选
     * @param string $field 筛选项
     * @param string $date  日期字符串   YYYY-mm-dd|YYYY-mm
     * @return array
     * @throws NotFoundHttpException
     */
    private function getUserIdsByDataField($type, $field, $date)
    {
        if (!in_array($type, ['day', 'month'])) {
            throw new NotFoundHttpException('type 参数错误');
        }
        $ids = [];
        $perf = new Perf();
        $fun = 'getDay' . ucfirst($field);
        if ('day' === $type) {
            if (!in_array($field, ['investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin', 'repayUser'])) {
                throw new NotFoundHttpException("日统计的 field 参数目前只支持 'investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin', 'repayUser'");
            }
            $ids = $perf->{$fun}($date);
        } else {
            if (!in_array($field, ['investor', 'newRegisterAndInvestor', 'newInvestor'])) {
                throw new NotFoundHttpException("月统计的 field 参数目前只支持 'investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin'");
            }
            $num = date('t', strtotime($date));
            $ids = [];
            for ($i = 1; $i <= $num; $i++) {
                $newDate = $date . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $res = $perf->{$fun}($newDate);
                $ids = array_merge($ids, $res);
            }
            $ids = array_unique($ids);
        }
        return $ids;
    }

    public function actionList($type, $field, $date, $result = 0)
    {
        $ids = $this->getUserIdsByDataField($type, $field, $date);

        $query = User::find()->where(['type' => 1])->andWhere(['in', 'id', $ids]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $query = $query->orderBy(['id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->sort = false;
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'type' => $type,
            'date' => $date,
            'field' => $field
        ]);
    }

    public function actionListExport($type, $field, $date)
    {
        $ids = $this->getUserIdsByDataField($type, $field, $date);

        $data = UserStats::collectLenderData(['in', '`user`.id', $ids]);
        UserStats::exportAsXlsx($data);
    }

    /**
     * 分销商数据统计(注册人数、购买人数、购买金额).
     *
     * 1. 可以根据输入的时间区间做统计;
     */
    public function actionAffiliation($start = null, $end = null)
    {
        $start = $start ? strtotime($start) : null;
        $end = $end ? strtotime($end.' 23:59:59') : null;
        $data = $this->affiliationStats($start, $end);
        $loanBalance = $this->affiliationBalance();                      //分销商的贷后余额
        $allData = $this->mergeAffiliationData($data, $loanBalance);     //分销商数据统计合并贷后余额
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);
        $pages = new Pagination([
            'totalCount' => count($data),
        ]);
        return $this->render('affiliation', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'start' => $start,
            'end' => $end,
        ]);
    }

    /**
     * 分销商数据统计导出(注册人数、购买人数、购买金额).
     *
     * 1. 可以根据输入的时间区间做统计;
     */
    public function actionAffiliationExport($start, $end)
    {
        if (!preg_match('/^[0-9]+$/', $start)) {
            $start = null;
        }

        if (!preg_match('/^[0-9]+$/', $end)) {
            $end = null;
        }

        $data = $this->affiliationStats($start, $end);
        $loanBalance = $this->affiliationBalance();       //分销商的贷后余额
        $allData = $this->mergeAffiliationData($data, $loanBalance);  //分销商数据统计合并贷后余额
        $record = implode(',', [
                '分销商ID',
                '分销商名称',
                '注册人数（人）',
                '投资人数（人）',
                '贷后余额（元）',
                '投资金额（元）'
            ]) . "\n";
        foreach ($allData as $v) {
            $array = [
                $v['id'],
                Html::encode($v['name']),
                intval($v['uc']),
                intval($v['oc']),
                floatval($v['fm']),
                floatval($v['m'])
            ];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $fileName = 'day-count(';

            if ($start) {
                $fileName .= date('Y-m-d', $start);
            }

            if ($end) {
                $fileName .= '到'.date('Y-m-d', $end);
            } else {
                $fileName .= '到'.date('Y-m-d');
            }

            $fileName .= ').csv';

            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    /**
     * 平台复投率(根据开始日期和结束日期输出提现金额，提现人数，复投总额，复投人数，回款总额，回款人数，新增总额，复投率),
     * @param string $startDate 没有选择开始日期时默认值为null，获取到的是当前月份1日，选择开始日期并搜索时值为选择月份1日
     * @param string $endDate   没有选择结束日期时默认值为null，获取到的是当前日期前一天，选择结束日期并搜索时值为选择月份最后一天
     * @param string $aff_id    复投率种类：平台复投
     * @throws \Exception     回款数据表(online_repayment_plan)中没有找到相应用户的userId还款数据时，抛出异常
     */
    public function actionPlatformRate($aff_id = '1',$startDate = null, $endDate = null)
    {
        $startDate = $startDate ? date('Y-m-d',strtotime($startDate)) : null;
        $endDate = $startDate ? date('Y-m-d',strtotime($endDate)) : null;
        if (empty($startDate) || false === strtotime($startDate)) {
            $startDate = date('Y-m-01');
        }
        if (empty($endDate) || false === strtotime($endDate)) {
            if (date('d') === '01') {
                $endDate = date('Y-m-d');
            } else {
                $endDate = date('Y-m-d',strtotime('-1 day'));
            }

        }
        //融资用户id统计
        $orgUsersData = Yii::$app->db->createCommand(
            "SELECT id FROM `user` WHERE 
            type = 2"
        )->queryAll();
        $orgUsers = array_column($orgUsersData , 'id');
        $orgUsersToString = implode(',', $orgUsers);
        //提现数据
        $drawCount = 0;
        $drawAmount = 0;
        $drawData = Yii::$app->db->createCommand(
            "SELECT COUNT( DISTINCT uid ) as drawUser, SUM( money ) as drawAmount 
            FROM  `draw_record` 
            WHERE STATUS = 2 
            AND uid
            NOT IN (" . $orgUsersToString .") 
            AND DATE( FROM_UNIXTIME( created_at ) ) 
            BETWEEN  :startDate
            AND  :endDate", [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryOne();

        if (!empty($drawData)) {
            $drawCount = $drawData['drawUser'];
            $drawAmount = $drawData['drawAmount'];
        }

        //输出到页面数据
        $fileData['drawAmount'] = number_format($drawAmount,2);  //提现金额
        $fileData['drawCount'] = $drawCount;                              //提现人数
        $fileData['startDate'] = $startDate;                              //开始日期
        $fileData['endDate'] = $endDate;                                  //结束日期
        $fileData['aff_id'] = $aff_id;                                    //复投率种类，平台复投

        //回款数据
        $refundData = Yii::$app->db->createCommand(
            "SELECT uid, SUM( benxi ) AS amount
            FROM online_repayment_plan
            WHERE STATUS IN ( 1, 2 ) 
            AND DATE(  `actualRefundTime` ) 
            BETWEEN  :startDate
            AND  :endDate
            AND benxi >0
            GROUP BY uid", [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();
        $refundCount = count($refundData);
        if ($refundCount === 0) {
            $fileData['message'] = "指定时间段内没有还款数据 ";
        }else {
            $refundAllUsers = array_column($refundData, 'uid');
            $refundAllAmount = ArrayHelper::index($refundData, 'uid');
            $refundUserToString = implode(',', $refundAllUsers);
            $refundAmount = array_sum(array_column($refundAllAmount, 'amount'));
            $reinvestAmount = 0;
            $increaseInvestAmount = 0;
            $newUserInvestData = [];

            //既有回款又有投资，并且不是首投用户 投资数据
            $sql = "SELECT o.uid,sum(o.order_money) as amount
                    FROM online_order AS o
                    INNER JOIN user_info AS i ON o.uid = i.user_id
                    WHERE o.`status` =1
                    AND DATE( FROM_UNIXTIME( o.order_time ) ) 
                    BETWEEN  :startDate
                    AND  :endDate
                    AND o.uid
                    IN (" . $refundUserToString . ")
                    AND i.firstInvestDate != i.lastInvestDate
                    AND o.order_money > 0.1
                    group by o.uid
                    ";
            $userInvestData = Yii::$app->db->createCommand($sql, [
                'startDate' => $startDate,
                'endDate' => $endDate,
            ])->queryAll();

            //转让标的既有回款，又有投资的投资数据
            $credit_sql = "SELECT user_id as uid, sum(amount)/100 as amount
                FROM credit_order 
                WHERE status = 1  
                AND DATE(createTime) 
                BETWEEN :startDate 
                AND :endDate 
                AND user_id IN (" . $refundUserToString . ")
                AND amount > 10
                GROUP BY user_id";
            $creditUserInvestData = Yii::$app->db_tx->createCommand($credit_sql, [
                'startDate' => $startDate,
                'endDate' => $endDate
            ])->queryAll();

            $userInvestData = array_merge($userInvestData, $creditUserInvestData);
            //将用户id相同的标的投资和转让投资合并，金额相加，
            foreach ($userInvestData as $value) {
                if (!isset($newUserInvestData[$value['uid']])) {
                    $newUserInvestData[$value['uid']] = $value;
                } else {
                    $newUserInvestData[$value['uid']]['amount'] += $value['amount'];
                }
            }
            $reinvestUserCount = count($newUserInvestData);
            //统计每个用户的 复投金额 和 新增金额
            foreach ($newUserInvestData as $item) {
                $userId = $item['uid'];
                $amount = $item['amount'];
                if (!isset($refundAllAmount[$userId])) {
                    throw new \Exception("没有找到 $userId 的还款数据");
                }

                //回款金额
                $userRefundAmount = $refundAllAmount[$userId]['amount'];

                //复投金额
                if ($amount > $userRefundAmount) {
                    $reinvestAmount = bcadd($reinvestAmount, $userRefundAmount, 2);
                    $increaseInvestAmount = bcadd($increaseInvestAmount, bcsub($amount, $userRefundAmount, 2), 2);
                } else {
                    $reinvestAmount = bcadd($reinvestAmount, $amount, 2);
                }
            }
            $rate = bcmul(bcdiv($reinvestAmount, $refundAmount, 4), 100, 2);
            $fileData['reinvestAmount'] = number_format($reinvestAmount, 2);                  //复投总额
            $fileData['reinvestUserCount'] = $reinvestUserCount;                                       //复投人数
            $fileData['refundAmount'] = number_format($refundAmount, 2);                      //回款总额
            $fileData['refundCount'] = $refundCount;                                                   //回款人数
            $fileData['increaseInvestAmount'] = number_format($increaseInvestAmount, 2);      //新增总额
            $fileData['rate'] = $rate;                                                                 //复投率
            $fileData['message'] = null;
        }

        return $this->render('platform_rate', $fileData);
    }
    //统计渠道用户注册及投资转化率信息
    public function actionChannelUserInfo($startDate = null, $endDate = null, $label = null)
    {
        $startDate = $startDate ? date('Y-m-d', strtotime($startDate)) : null;
        $endDate = $startDate ? date('Y-m-d', strtotime($endDate)) : null;
        if (empty($startDate) || false === strtotime($startDate)) {
            $startDate = date('Y-m-01');
        }
        if (empty($endDate) || false === strtotime($endDate)) {
            if (date('d') === '01') {
                $endDate = date('Y-m-d');
            } else {
                $endDate = date('Y-m-d', strtotime('-1 day'));
            }
        }
        $pageSize = 10;
        //从piwik中获取各渠道商名称及页面访问数量
        $piwikData = Piwik::getChannelUserNum($startDate, $endDate);
        $registerUserData = Yii::$app->db->createCommand('SELECT
            count(id) as registerUserCount,
            campaign_source
            FROM user  
            WHERE DATE(FROM_UNIXTIME(created_at)) 
            BETWEEN :startDate 
            AND :endDate
            GROUP BY campaign_source', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->queryAll();
        $registerUserData = ArrayHelper::index($registerUserData, 'campaign_source');
        //查询时间段内各渠道注册人数及注册购买总金额,订单数量和订单金额
        $registerData = Yii::$app->db->createCommand('SELECT 
          u.campaign_source AS campaign_source,
          sum(ui.firstInvestAmount) AS firstInvestAmount,
          count(o.id) as registerOrderCount,
          sum(o.order_money) as registerOrderMoneySum 
          FROM user u 
          LEFT JOIN user_info ui
          ON ui.user_id = u.id 
          LEFT JOIN online_order o 
          ON o.uid = u.id 
          WHERE o.status = 1 
          AND DATE(FROM_UNIXTIME(u.created_at)) 
          BETWEEN :startDate 
          AND :endDate
          GROUP BY u.campaign_source', [
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->queryAll();
        $registerData = ArrayHelper::index($registerData, 'campaign_source');
        //查询时间段内各渠道购买订单数量及订单总额
        $orderData = Yii::$app->db->createCommand('SELECT 
            o.campaign_source AS campaign_source, 
            count(o.id) AS orderCount,
            sum(o.order_money) AS orderMoneySum
            FROM online_order o 
            WHERE o.status = 1 
            AND DATE(FROM_UNIXTIME(o.created_at)) 
            BETWEEN :startDate 
            AND :endDate
            GROUP BY o.campaign_source', [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();
        $orderData = ArrayHelper::index($orderData, 'campaign_source');
        $campaignSource = array_keys($orderData);
        $piwikCampaignSource = array_keys($piwikData);
        $campaignSource =array_merge($campaignSource, $piwikCampaignSource);
        $campaignSource = array_unique($campaignSource);
        $allData = [];
        $nbVisitsArray = [];
        foreach ($campaignSource as $campaign) {
            $allData[$campaign]['label'] = $campaign;
            $nbVisits = count($piwikData[$campaign]) ? $piwikData[$campaign]['nb_visits'] : 0;
            $nbVisitsArray[] = $nbVisits;
            $allData[$campaign]['nb_visits'] = $nbVisits;
            $allData[$campaign]['firstInvestAmount'] = count($registerData[$campaign]) ? $registerData[$campaign]['firstInvestAmount'] : 0; //新注册购买总额
            $allData[$campaign]['registerOrderCount'] = count($registerData[$campaign]) ? $registerData[$campaign]['registerOrderCount'] : 0;//订单数量
            //订单金额
            $allData[$campaign]['registerOrderMoneySum'] = count($registerData[$campaign]) ? $registerData[$campaign]['registerOrderMoneySum'] : 0;
            $allData[$campaign]['orderMoneySum'] = count($orderData[$campaign]) ? $orderData[$campaign]['orderMoneySum'] : 0; //渠道订单金额
            $registerUserCount = count($registerUserData[$campaign]) ? $registerUserData[$campaign]['registerUserCount'] : 0;//注册人数
            $orderCount = count($orderData[$campaign]) ? $orderData[$campaign]['orderCount'] : 0;
            $allData[$campaign]['registerUserCount'] = $registerUserCount; //注册人数
            $allData[$campaign]['orderCount'] = $orderCount; //渠道订单数量
            $allData[$campaign]['registerConversionRate'] = 0;  //注册转化率
            $allData[$campaign]['orderConversionRate'] = 0; //订单转化率
            if ($nbVisits) {
                $allData[$campaign]['registerConversionRate'] = $registerUserCount/$nbVisits*100;
                $allData[$campaign]['orderConversionRate'] = $orderCount/$nbVisits*100;
            }
        }
        array_multisort($nbVisitsArray, SORT_DESC, $allData);
        if (strlen($label) !== 0) {
            $label = str_replace('，', ',', $label);
            $labelArray = explode(',', $label);
            $newData = [];
            foreach ($labelArray as $labelValue) {
                $newData[$labelValue] = $allData[$labelValue];
            }
            unset($allData);
            $allData = $newData;
        }

        $totalCount = count($allData);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
            'pagination' => [
                'pageSize' => $pageSize
            ]
        ]);
        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
        return $this->render('channel_user_info', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'label' => $label,
        ]);
    }
//新手标人数统计页面
    public function actionXinshoutj($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? date('Y-m-d',strtotime($startDate)) : null;
        $endDate = $startDate ? date('Y-m-d',strtotime($endDate)) : null;
        if (empty($startDate) || false === strtotime($startDate)) {
            $startDate = date('Y-m-01');
        }
        if (empty($endDate) || false === strtotime($endDate)) {
                $endDate = date('Y-m-d',time());
        }
        $exportData = [];
        //startDate到endDate时间内新注册用户购买新手标信息
        $xsData = Yii::$app->db->createCommand(
            "SELECT  DISTINCT(o.uid), 
            DATE(FROM_UNIXTIME(o.created_at)) as createdTime,
            DATE(FROM_UNIXTIME(p.finish_date)) AS finishDate 
            FROM online_order AS o 
            INNER JOIN user AS u ON o.uid = u.id 
            INNER JOIN online_product AS p on o.online_pid = p.id 
            WHERE p.`is_xs` = 1 
            AND o.status = 1 
            AND p.finish_date >0 
            AND DATE(FROM_UNIXTIME(u.created_at)) 
            BETWEEN :startDate 
            AND :endDate 
            and Date(FROM_UNIXTIME(o.created_at)) 
            BETWEEN :startDate 
            AND :endDate", [
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->queryAll();
        $xsAndOtherData = [];
        $xsAndDrawData = [];
        $reOrderData = [];

        if (!empty($xsData)) {
            $xsAllUsers =array_column($xsData,'uid');
            $xsAllUserToString = implode(',',$xsAllUsers);
            //用户投资新手标后投资其他标的的信息
            $refundOtherData = Yii::$app->db->createCommand(
                "SELECT DISTINCT(o.uid), 
                DATE(FROM_UNIXTIME(o.created_at)) AS orderCreatedTime 
                FROM online_order AS  o 
                INNER JOIN online_product as p ON o.online_pid = p.id 
                WHERE o.status = 1 
                AND p.is_xs = 0 
                AND o.uid IN (" . $xsAllUserToString . ") 
                AND DATE(FROM_UNIXTIME(o.created_at)) >= :startDate", [
                    'startDate' =>$startDate
            ])->queryAll();
            $refundAllUsers = array_column($refundOtherData, 'uid');
            $unRefundUsers = array_diff($xsAllUsers, $refundAllUsers);
            if (!empty($unRefundUsers)) {
                $unRefundUsersToString = implode(',', $unRefundUsers);
                $drawData = Yii::$app->db->createCommand(
                    "SELECT DISTINCT(uid), 
                DATE(FROM_UNIXTIME(created_at)) AS drawTime 
                FROM draw_record
                WHERE status = 2 
                AND uid IN (" . $unRefundUsersToString . ") 
                AND created_at >= :startDate",[
                    'startDate' =>$startDate
                ])->queryAll();
            }

            foreach ($xsData as $value) {
                $newEndTime = $value['finishDate'];
                foreach ($refundOtherData as $item) {
                    if (!empty($item) && $value['uid'] === $item['uid']) {
                        if ($item['orderCreatedTime'] >= $value['createdTime'] && $item['orderCreatedTime'] <= $newEndTime) {
                            if (!in_array($item['uid'], $xsAndOtherData)) {
                                array_push($xsAndOtherData,$item['uid']);
                            }
                        }
                        if ($item['orderCreatedTime'] > $newEndTime) {
                            if (!in_array($item['uid'], $reOrderData)) {
                                array_push($reOrderData,$item['uid']);
                            }
                        }
                    }
                    if (!empty($unRefundUsers)) {
                        foreach ($drawData as $draw) {
                            if (!empty($draw)
                                && $value['uid'] === $draw['uid']
                                && $draw['drawTime'] > $newEndTime
                            ) {
                                if (!in_array($draw['uid'], $xsAndDrawData)) {
                                    array_push($xsAndDrawData, $draw['uid']);
                                }

                            }
                        }
                    }
                }
            }
        }
        $exportData['xsCount'] = count($xsData);                      //新注册用户购买新手标人数
        $exportData['xsAndOtherCount'] = count($xsAndOtherData);      //新注册用户购买新手标还买了其他标的的人数
        $exportData['xsAndDrawCount'] = count($xsAndDrawData);        //只购买新手标到期提现人数
        $exportData['reOrderCount'] = count($reOrderData);            //购买新手标到期复投人数
        $exportData['startDate'] = $startDate;                        //查询开始时间
        $exportData['endDate'] = $endDate;                            //查询结束时间

        return $this->render('xinshoutj',$exportData);
    }

    /**
     * 根据订单日期区间查询分销商统计数据.
     */
    private function affiliationStats($start, $end)
    {
        $userCond = "u.id = ua.`user_id` AND u.type = 1";
        $orderCond = "o.`uid` = ua.`user_id` AND o.status = 1";

        if ($start) {
            $userCond .= " AND u.created_at >= $start";
            $orderCond .= " AND o.created_at >= $start";
        }

        if ($end) {
            $userCond .= " AND u.created_at <= $end";
            $orderCond .= " AND o.created_at <= $end";
        }
        $sql = "SELECT 
            a.id, 
            a.name, 
            COUNT(DISTINCT u.id) AS uc, 
            COUNT(DISTINCT o.`uid`) AS oc, 
            SUM(o.`order_money`) AS m
            FROM affiliator AS a
            LEFT JOIN user_affiliation AS ua ON a.id = ua.`affiliator_id`
            LEFT JOIN `user` AS u ON ".$userCond."
            LEFT JOIN online_order AS o ON ".$orderCond." GROUP BY a.id;";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
    /**
     * 查询分销商的贷后余额
     */
    private function affiliationBalance()
    {
        $orderCond = "o.`uid` = ua.`user_id` AND o.status = 1";
        $productCond = "o.`online_pid` = op.`id` 
        where 
        op.status = 2 OR 
        op.status = 3 OR 
        op.status = 5 OR 
        op.status = 7 ";
        $sql = "SELECT 
            a.id,
            SUM(o.`order_money`)  AS fm
            FROM affiliator AS a
            LEFT JOIN user_affiliation AS ua ON a.id = ua.`affiliator_id`
            LEFT JOIN online_order AS o ON ".$orderCond." 
            LEFT JOIN online_product AS op ON ".$productCond." GROUP BY a.id;";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    /**
     *  分销商数据统计数据合并贷后余额数据
     * @param array $data  分销上统计数据
     * @param array $loanBalance  分销商贷后余额
     * @return array  合并后返回的数组
     */
    private function mergeAffiliationData($data,$loanBalance)
    {
        foreach ($data as $key => $value) {
            foreach ($loanBalance as $v) {
                if ($value['id'] === $v['id']) {
                    $data[$key]['fm'] = $v['fm'];
                }
            }
        }
        return $data;
    }

    function createdTimeAddExpires($createdTime, $expires)
    {
        return $createdTime+$expires;
    }
}
