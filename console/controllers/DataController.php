<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\draw\DrawManager;
use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineRepaymentRecord;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\Award;
use common\models\promo\FirstOrderPoints;
use common\models\promo\InviteRecord;
use common\models\promo\LoanOrderPoints;
use common\models\promo\Poker;
use common\models\promo\PokerUser;
use common\models\stats\Perf;
use common\models\transfer\Transfer;
use common\models\user\MoneyRecord;
use common\models\user\OriginalBorrower;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\AccountService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use common\utils\TxUtils;
use common\view\LoanHelper;
use PHPExcel_IOFactory;
use wap\modules\promotion\models\RankingPromo;
use Wcg\Math\Bc;
use yii\console\Controller;
use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class DataController extends Controller
{
    //导出投资用户信息 data/export-lender-data
    public function actionExportLenderData()
    {
        $allData = UserStats::collectLenderData();
        $path = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/all_investor_user.xlsx';
        if (file_exists($file)) {
            unlink($file);
        }
        $objPHPExcel = UserStats::initPhpExcelObject($allData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    //立合旺通数据导出（一天时更新一次）data/export-issuer-record
    public function actionExportIssuerRecord($issuerId = 1)
    {
        $issuerId = intval($issuerId);
        $issuerData = Issuer::getIssuerRecords($issuerId);//获取立合旺通数据
        $path = rtrim(Yii::$app->params['backend_tmp_share_path'], '/');
        $file = $path . '/lihewangtong.xlsx';
        if (file_exists($file)) {
            unlink($file);
        }
        $exportData[] = ['期数', '融资方', '发行方', '底层融资方', '项目名称', '项目编号', '项目状态', '备案金额（元）', '募集金额（元）', '实际募集金额（元）', '年化收益率（%）', '开始融资时间', '满标时间', '起息日', '还款本金', '还款利息', '预计还款时间', '实际还款时间'];
        $records = $issuerData['model'];
        $issuer = $issuerData['issuer'];
        $repaymentPlan = $issuerData['plan'];
        $refundTime = $issuerData['refundTime'];
        $ob = OriginalBorrower::find()->asArray()->all();
        $ob = ArrayHelper::map($ob,'id','name');
        foreach ($records as $key => $loan) {
            $obIds = [];
            if (!empty($loan->original_borrower_id)) {
                $obIds = explode(',', $loan->original_borrower_id);
            }
            $originalBorrower = '';
            foreach ($obIds as $obId) {
                $originalBorrower .= $ob[$obId].";";
            }
            $originalBorrower = rtrim($originalBorrower, ';');
            if (isset($repaymentPlan[$key])) {
                foreach ($repaymentPlan[$key] as $repayment) {
                    $exportData[] = [
                        $repayment['qishu'],
                        $loan->borrower->org_name,
                        $issuer->name,
                        $originalBorrower,
                        $loan->title,
                        $loan->issuerSn,
                        \Yii::$app->params['deal_status'][$loan->status],
                        floatval($loan->filingAmount),
                        floatval($loan->money),
                        floatval($loan->funded_money),
                        LoanHelper::getDealRate($loan) . ($loan->jiaxi ? '+' . StringUtils::amountFormat2($loan->jiaxi) : ''),
                        empty($loan->start_date) ? '---' : date('Y-m-d', $loan->start_date),
                        empty($loan->full_time) ? '---' : date('Y-m-d', $loan->full_time),
                        empty($loan->jixi_time) ? '---' : date('Y-m-d', $loan->jixi_time),
                        floatval($repayment['totalBenjin']),
                        floatval($repayment['totalLixi']),
                        date('Y-m-d', $repayment['refund_time']),
                        isset($refundTime[$key][$repayment['qishu']]) ? date('Y-m-d', $refundTime[$key][$repayment['qishu']]) : '---'
                    ];
                }
            } else {
                $exportData[] = [
                    '---',
                    $loan->borrower->org_name,
                    $issuer->name,
                    $originalBorrower,
                    $loan->title,
                    $loan->issuerSn,
                    \Yii::$app->params['deal_status'][$loan->status],
                    floatval($loan->filingAmount),
                    floatval($loan->money),
                    floatval($loan->funded_money),
                    LoanHelper::getDealRate($loan) . ($loan->jiaxi ? '+' . StringUtils::amountFormat2($loan->jiaxi) : ''),
                    empty($loan->start_date) ? '---' : date('Y-m-d', $loan->start_date),
                    empty($loan->full_time) ? '---' : date('Y-m-d', $loan->full_time),
                    empty($loan->jixi_time) ? '---' : date('Y-m-d', $loan->jixi_time),
                    '---',
                    '---',
                    '---',
                    '---'
                ];
            }
        }

        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    //导出每个用户投资每个标的|转让的数据:工具脚本 data/export-user161230
    public function actionExportUser161230()
    {
        $sql = "SELECT u.id AS user_id, u.real_name AS name, u.mobile AS mobile, u.idcard AS idcard, p.id AS loan_id, p.title title, FROM_UNIXTIME( p.finish_date ) AS finish_date, SUM( rp.benjin ) AS benjin, SUM( rp.lixi ) AS all_lixi, MAX( o.yield_rate ) AS rate
FROM online_repayment_plan AS rp
INNER JOIN user AS u ON rp.uid = u.id
INNER JOIN online_product AS p ON rp.online_pid = p.id
INNER JOIN online_order AS o ON rp.order_id = o.id
WHERE u.type =1
AND o.status =1
GROUP BY u.id, rp.online_pid
ORDER BY u.real_name ASC , rp.online_pid ASC ";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            $result[$item['user_id']][$item['loan_id']] = [
                'name' => $item['name'],
                'mobile' => strval($item['mobile']) . "\t",
                'idcard' => strval($item['idcard']) . "\t",
                'title' => $item['title'],
                'finish_date' => $item['finish_date'],
                'rate' => floatval($item['rate']) * 100,
                'benjin' => floatval($item['benjin']),
                'all_lixi' => floatval($item['all_lixi']),
            ];
        }

        //已还
        $sql = "SELECT rp.uid AS user_id, rp.online_pid AS loan_id, SUM( rp.lixi ) AS y_lixi
FROM online_repayment_plan AS rp
WHERE rp.status
IN ( 1, 2 )
GROUP BY rp.uid, rp.online_Pid";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            if (isset($result[$item['user_id']][$item['loan_id']])) {
                $result[$item['user_id']][$item['loan_id']]['y_lixi'] = floatval($item['y_lixi']);
            }
        }
        //未还
        $sql = "SELECT rp.uid AS user_id, rp.online_pid AS loan_id, SUM( rp.lixi ) AS n_lixi
FROM online_repayment_plan AS rp
WHERE rp.status = 0
GROUP BY rp.uid, rp.online_Pid";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        foreach ($data as $item) {
            if (isset($result[$item['user_id']][$item['loan_id']])) {
                $result[$item['user_id']][$item['loan_id']]['n_lixi'] = floatval($item['n_lixi']);
            }
        }
        $file = 'user_loan_data' . date('YmdHis') . '.csv';
        header('Content-Disposition: attachment; filename="' . $file . '"');
        $out = fopen($file, 'w');

        fputs($out, "\xEF\xBB\xBF");//添加BOM头
        fputcsv($out, ['姓名', '手机号', '身份证号', '标的标题', '截止日期', '利率(%)', '投资金额', '总利息', '已还利息', '未还利息']);
        if (!empty($result)) {
            foreach ($result as $user_id => $userData) {
                if (!empty($userData) and  is_array($userData)) {
                    foreach ($userData as $loan_id => $loanData) {
                        if (!empty($loanData) && is_array($loanData)) {
                            fputcsv($out, $loanData);
                        }
                    }
                }
            }
        }

        fclose($out);
        exit();
    }

    /**
     * 补发首投积分和普通积分:修复工具
     * @param $order_id
     */
    public function actionAddPoints($order_id) {
        $order = OnlineOrder::findOne($order_id);
        if (is_null($order)) {
            return;
        }
        //首投送积分活动
        $promo1 = RankingPromo::find()->where(['key' => 'first_order_point'])->one();
        if (!is_null($promo1)) {
            $model1 = new FirstOrderPoints($promo1);
            if ($model1->canSendPoint($order)) {
                $model1->addUserPoints($order);
            }
        }
        $promo2 = RankingPromo::find()->where(['key' => 'loan_order_points'])->one();
        if (!is_null($promo2)) {
            $model2 = new LoanOrderPoints($promo2);
            if ($model2->canSendPoint($order)) {
                $model2->addUserPointsWithLoanOrder($order);
            }
        }
    }

    /**
     * 修复标的工具:修复工具
     *
     * 联动成功,但是本地提现记录缺失.
     */
    public function actionDraw($pid = 1812)
    {
        $onlineProduct = OnlineProduct::findOne($pid);
        if (!$onlineProduct) {
            $this->stdout('标的信息不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        $onlineFangkuan = OnlineFangkuan::findOne(['online_product_id' => $pid]);
        if (!$onlineFangkuan) {
            $this->stdout('放款记录不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        if (!$this->allowDraw($onlineFangkuan)) {
            $this->stdout('当前放款状态不允许提现操作');

            return Controller::EXIT_CODE_ERROR;
        }

        $account = UserAccount::findOne(['uid' => $onlineFangkuan->uid, 'type' => UserAccount::TYPE_BORROW]);
        if (!$account) {
            $this->stdout('融资用户账户信息不存在');

            return Controller::EXIT_CODE_ERROR;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            //融资方放款,不收取手续费
            $draw = DrawManager::initDraw($account, $onlineFangkuan->order_money);
            if (!$draw->save()) {
                throw new \Exception('提现申请失败', '000003');
            }

            $draw->orderSn = $onlineFangkuan->sn;
            if (!$draw->save()) {
                throw new \Exception('写入放款流水失败', '000003');
            }

            $onlineFangkuan->status = OnlineFangkuan::STATUS_TIXIAN_APPLY;
            if (!$onlineFangkuan->save()) {
                throw new \Exception('修改放款审核状态失败', '000003');
            }

            DrawManager::ackDraw($draw);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->stdout($e->getMessage());

            return Controller::EXIT_CODE_ERROR;
        }

        $this->stdout('放款成功!');
        return Controller::EXIT_CODE_NORMAL;
    }

    private function allowDraw(OnlineFangkuan $fangkuan)
    {
        return in_array($fangkuan->status, [
            OnlineFangkuan::STATUS_FANGKUAN,
            OnlineFangkuan::STATUS_TIXIAN_FAIL,
        ]);
    }

    //给邀请者补发投资红包:修复工具 php yii data/pay-cash
    public function actionPayCash($orderId)
    {
        $order = OnlineOrder::findOne($orderId);
        if (!is_null($order)) {
            $inviteRecord = InviteRecord::findOne(['invitee_id' => $order->uid]);
            if (!is_null($inviteRecord)) {
                $count = OnlineOrder::find()->where(['uid' => $inviteRecord->user_id, 'status' => 1])->count();
                $count = intval($count);
                if ($count > 0) {
                    $money = round($order->order_money / 1000, 1);
                    $this->stdout('为ID为 '.$inviteRecord->user_id.' 的用户补发红包 '. $money . ' 元'. PHP_EOL);
                    $moneyRecord = $this->sendUserCash($inviteRecord->user_id, $money);
                    if (is_bool($moneyRecord) && !$moneyRecord) {
                        $this->stdout($order->uid.'发送现金红包'.$money.'元失败！');
                    }
                    $promo = RankingPromo::find()
                        ->where(['key' => 'promo_invite_12'])
                        ->one();
                    if (null !== $promo && $moneyRecord instanceof MoneyRecord) {
                        $award = Award::cashAward($inviteRecord->user, $promo, $moneyRecord);
                        $award->save(false);
                        $this->stdout($inviteRecord->user_id.$money.'元现金红包成功！');
                    }
                }
            }
        }
    }

    //给用户发红包
    private function sendUserCash($userId, $money)
    {
        $user = User::findOne($userId);
        if (!is_null($user)) {
            $money = max(floatval($money), 0.01);
            return AccountService::userTransfer($user, $money);
        }
        return false;
    }

    /**
     * 为 sn 是 null 的 transfer 添加 sn
     * php yii data/add-sn
     *
     * 临时代码，时用完删除，2017-06-19
     */
    public function actionAddSn()
    {
        $transfers = Transfer::find()->where(['sn' => null, 'status' => Transfer::STATUS_INIT])->all();
        $count = count($transfers);
        $this->stdout("用{$count}条记录待处理 \n");
        $count=0;
        /**
         * @var Transfer $transfer
         */
        foreach ($transfers as $transfer) {
            $transfer->sn = TxUtils::generateSn('Tr');
            $transfer->save(false);
            $count++;
        }
        $this->stdout("成功处理{$count}条 \n");
    }

    /**
     * 返回投资新手标在xx-xx日之间还款的客户信息-excel文件
     *
     * @param string      $startDate 开始日期 形如：20170901
     * @param null|string $endDate   结束日期 形如：20170915
     *
     */
    public function actionXsDueListExport($startDate, $endDate = null)
    {
        if (null === $endDate) {
            $endDate = date('Ymd');
        }
        $file = Yii::getAlias('@app/runtime/xs_'.$startDate.'_'.$endDate.'.xlsx');
        $exportData[] = ['姓名', '手机号', '到期时间', '到期金额', '分销商'];
        $sql = "select u.real_name,u.safeMobile,from_unixtime(o.refund_time) as refund_time,o.benxi,a.name 
from online_repayment_record o 
inner join online_product p on o.online_pid = p.id 
inner join user u on u.id = o.uid 
left join user_affiliation ua on ua.user_id = o.uid 
left join affiliator a on a.id = ua.affiliator_id 
where date(from_unixtime(o.refund_time)) >= :startDate 
and date(from_unixtime(o.refund_time)) <= :endDate 
and o.status in (1,2) 
and p.is_xs = 1 
and p.isTest = 0 
order by o.refund_time asc";
        $result = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        foreach ($result as $data) {
            $exportData[] = [
                $data['real_name'],
                strval(SecurityUtils::decrypt($data['safeMobile'])),
                $data['refund_time'],
                $data['benxi'],
                $data['name'],
            ];
        }

        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 返回理财资产大于等于一定值的用户信息EXCE文件名（包含线上线下）
     * 文件名 order_asset_ge_'.$assetMoney.'_'.date('YmdHis').'.xlsx'
     *
     * 导出项： 姓名、手机号、身份证号、年龄、理财资产、性别
     *
     * @param int $assetMoney 资产金额限制
     */
    public function actionPickUserOrderAsset($assetMoney = 1000000)
    {
        $db = Yii::$app->db;
        /**
         * 线上用户信息构造
         * [
         *      'idCard1' => [
         *          'realName' => 'xxxx',
         *          'mobile' => '14558541343',
         *          'idCard' => '110106199108010447',
         *          'age' => '26',
         *          'orderAsset' => '46100.00',
         *          'gender' => '女',
         *      ],
         *      'idCard2' => [
         *          ...
         *      ],
         *      ...
         * ]
         */
        $onlineSql = "select 
u.real_name as realName, 
u.safeMobile as mobile, 
u.safeIdCard as idCard, 
(DATE_FORMAT(NOW(), '%Y') - SUBSTRING(u.birthdate, 1, 4)) as age, 
ua.investment_balance as orderAsset, 
u.birthdate as birthDate, 
af.name as affiliatorName
from user u 
inner join user_account ua on ua.uid = u.id 
left join user_affiliation uf on uf.user_id = u.id
left join affiliator af on af.id = uf.affiliator_id
where ua.type = 1 
and u.idcard_status = 1
and ua.investment_balance >= 0
order by ua.investment_balance desc";
        $users = $db->createCommand($onlineSql)
            ->queryAll();
        $onlineUsers = [];
        foreach ($users as $user) {
            $idCard = SecurityUtils::decrypt($user['idCard']);
            $user['idCard'] = $idCard;
            $user['mobile'] = SecurityUtils::decrypt($user['mobile']);
            $user['gender'] = intval(substr($idCard, -2, 1)) % 2 ? '男' : '女';
            if (!array_key_exists($idCard, $onlineUsers)) {
                $onlineUsers[$idCard] = $user;
            } else {
                $onlineUsers[$idCard]['orderAsset'] += $user['orderAsset'];
            }
        }

        /**
         * 线下用户信息构造
         * [
         *      'idCard1' => [
         *          'realName' => 'xxxx',
         *          'mobile' => '14558541343',
         *          'idCard' => '110106199108010447',
         *          'age' => '26',
         *          'orderAsset' => '46100.00',
         *          'gender' => '女',
         *      ],
         *      'idCard2' => [
         *          ...
         *      ],
         *      ...
         * ]
         */
        $offlineSql = "SELECT 
u.realName, 
u.mobile, 
UPPER(u.idCard) AS idCard, 
(DATE_FORMAT(NOW(), '%Y') - SUBSTRING(u.idCard, 7, 4)) AS age, 
sum(o.money * 10000) AS orderAsset, 
SUBSTRING(u.idCard, 7, 8) as birthDate, 
af.name as affiliatorName, 
IF(SUBSTR(u.idCard, -2, 1) % 2, '男', '女') AS gender
from offline_order as o
inner join offline_user as u on o.user_id = u.id
inner join offline_loan as p on o.loan_id = p.id
left join affiliator as af on af.id = o.affiliator_id 
where o.isDeleted = 0
and curDate() < date(p.finish_date)
group by o.user_id
having orderAsset >= 0";
        $offUsers = $db->createCommand($offlineSql)
            ->queryAll();
        $offlineUsers = ArrayHelper::index($offUsers, 'idCard');

        //合并成最后的导出数组，并按照元素order排序，截取大于$assetMoney的用户信息
        foreach ($onlineUsers as $idCard => $onlineUser) {
            if (isset($offlineUsers[$idCard])) {
                $onlineUsers[$idCard]['orderAsset'] = bcadd($onlineUsers[$idCard]['orderAsset'], $offlineUsers[$idCard]['orderAsset'], 2);
                $onlineUsers[$idCard]['affiliatorName'] = $onlineUsers[$idCard]['affiliatorName'].','.$offlineUsers[$idCard]['affiliatorName'];
                unset($offlineUsers[$idCard]);
            }
        }
        $realUsers = array_merge($onlineUsers, $offlineUsers);
        ArrayHelper::multisort($realUsers, 'orderAsset', SORT_DESC, SORT_REGULAR);
        $chunkUsers = [];
        foreach ($realUsers as $k => $realUser) {
            if ($realUser['orderAsset'] < $assetMoney) {
                break;
            }
            $chunkUsers[$k] = $realUser;
        }

        //生成excel
        $title = ['姓名', '手机号', '身份证号', '年龄', '理财资产', '生日', '分销商', '性别'];
        array_unshift($chunkUsers, $title);
        $file = Yii::getAlias('@app/runtime/order_asset_ge_'.$assetMoney.'_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($chunkUsers);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 周周乐活动 - 中奖名单excel文件
     *
     * @param string $term  期数，如：20171016
     *
     * @return int
     */
    public function actionExportPokerAwardList($term)
    {
        $poker = Poker::find()
            ->where(['term' => $term])
            ->one();
        if (null === $poker) {
            $this->stdout($term.'期暂未开奖，无中奖结果');
            return self::EXIT_CODE_ERROR;
        }

        //获得本期开奖号码并查询所有的中奖用户
        $spade = $poker->spade;
        $u = User::tableName();
        $p = PokerUser::tableName();
        $list = PokerUser::find()
            ->select([
                "$u.safeMobile",
                "$u.real_name",
            ])->innerJoin($u, "$p.user_id = $u.id")
            ->where(['term' => $term])
            ->andWhere(['spade' => $spade])
            ->asArray()
            ->all();
        $awardList = [];
        foreach ($list as $k=>$value) {
            $awardList[$k]['mobile'] = SecurityUtils::decrypt($value['safeMobile']);
            $awardList[$k]['realName'] = null === $value['real_name'] ? '' : $value['real_name'];
        }

        //生成中奖名单excel
        $title = ['手机号','姓名'];
        array_unshift($awardList, $title);
        $file = Yii::getAlias('@app/runtime/poker_'.$term.'_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($awardList);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 统计一段时间内通过指定渠道码注册的用户信息情况
     * 脚本命令： php yii data/user-info-by-campaign-source 2017-10-24 2017-10-30
     * campaign_source由$this->getExportCampaignSources()提供
     *
     * @param string $startDate 注册开始日期
     * @param string $endDate   注册结束日期
     */
    public function actionUserInfoByCampaignSource($startDate, $endDate)
    {
        $sql = "select 
from_unixtime(u.created_at) 注册时间,u.campaign_source 渠道码,u.real_name 姓名,u.safeMobile 手机号,if(ub.id>0, 1, 0) 是否绑卡,ui.investTotal 投资总金额
from user u
left join user_info ui on ui.user_id = u.id
left join user_bank ub on ub.uid = u.id
where u.campaign_source in ('wzdsbczggt','zswzczggt','wzcjczggt')
and date(from_unixtime(u.created_at)) >= :startDate
and date(from_unixtime(u.created_at)) <= :endDate";
        $userInfo = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        //生成用户信息excel
        $title = ['注册时间', '渠道码', '姓名', '手机号', '是否绑卡', '投资总金额'];
        array_unshift($userInfo, $title);
        $file = Yii::getAlias('@app/runtime/userInfo_by_campaignSources_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($userInfo);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    public function actionUserViaBank($bankId)
    {
        $sql = "select u.safeMobile from user_bank ub inner join user u on ub.uid=u.id where u.type=1 and ub.bank_id=:bankId";
        $users = Yii::$app->db->createCommand($sql, [
            'bankId' => $bankId,
        ])->queryAll();
        foreach ($users as $k => $user) {
            $users[$k]['safeMobile'] = SecurityUtils::decrypt($user['safeMobile']);
        }

        $title = ['手机号'];
        array_unshift($users, $title);
        $file = Yii::getAlias('@app/runtime/user_via_bank_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($users);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    public function actionExportUser()
    {
        $ua = UserAccount::tableName();
        $u = User::tableName();
        $uf = UserAffiliation::tableName();
        $af = Affiliator::tableName();

        //线下数据处理
        $onlineUsers = User::find()
            ->select("$u.safeIdCard, $u.real_name, $u.safeMobile, $af.name, $ua.investment_balance")
            ->innerJoin($ua, "$ua.uid = $u.id")
            ->leftJoin($uf, "$uf.user_id = $u.id")
            ->leftJoin($af, "$af.id = $uf.affiliator_id")
            ->where(["$u.type" => User::USER_TYPE_PERSONAL])
            ->andWhere(["$u.idcard_status" => true])
            ->asArray()
            ->all();
        $sql = "SELECT 
u.idCard,
sum(o.money * 10000) AS orderAsset 
from offline_order as o 
inner join offline_user as u on o.user_id = u.id 
inner join offline_loan as p on o.loan_id = p.id 
where o.isDeleted = 0 and curDate() < date(p.finish_date)
group by o.user_id 
having orderAsset >= 0";

        $offlineUsers = Yii::$app->db->createCommand($sql)->queryAll();
        $offlineCards = ArrayHelper::getColumn($offlineUsers, 'idCard');
        $offlineUsers = ArrayHelper::index($offlineUsers, 'idCard');

        //线上数据处理
        foreach ($onlineUsers as $k => $onlineUser) {
            $onlineUsers[$k]['safeIdCard'] = SecurityUtils::decrypt($onlineUser['safeIdCard']);
            $onlineUsers[$k]['safeMobile'] = SecurityUtils::decrypt($onlineUser['safeMobile']);
        }
        $onlineUsers = ArrayHelper::index($onlineUsers, 'safeIdCard');
        $onlineCards = array_keys($onlineUsers);

        //数据排重
        $idcards = array_intersect(array_unique($onlineCards), $offlineCards);

        //开始拼凑导出数据
        $this->stdout('开始导出数据，用户数据总量'.count($idcards).'人');
        $data = [];
        //姓名、注册手机号、身份证号、分销商、线上线下分别的理财资产金额
        foreach ($idcards as $k => $idcard) {
            $data[$k]['realName'] = $onlineUsers[$idcard]['real_name'];
            $data[$k]['mobile'] = $onlineUsers[$idcard]['safeMobile'];
            $data[$k]['idCard'] = '\''.$idcard;
            $data[$k]['affiliatorName'] = $onlineUsers[$idcard]['name'];
            $data[$k]['onlineAsset'] = $onlineUsers[$idcard]['investment_balance'];
            $data[$k]['offlineAsset'] = $offlineUsers[$idcard]['orderAsset'];
        }

        //生成用户信息excel
        $title = ['姓名', '注册手机号', '身份证号', '分销商', '线上理财资产', '线下理财资产'];
        array_unshift($data, $title);
        $file = Yii::getAlias('@app/runtime/user_oo_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($data);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 截止到某一日期的累计收益金额cache，用户ID为键（DbCache）
     * 缓存暂定义为1年
     *
     * @param string $expireDate 截止日期
     */
    public function actionProfitCache($expireDate)
    {
        $record = OnlineRepaymentRecord::find()
                ->select('uid, sum(lixi) as total')
                ->where(['status' => [
                    OnlineRepaymentRecord::STATUS_DID,
                    OnlineRepaymentRecord::STATUS_BEFORE,
                ]])->andFilterWhere(['<=', 'date(from_unixtime(refund_time))', $expireDate])
                ->groupBy(['uid'])
                ->orderBy(['total' => SORT_ASC])
                ->asArray()
                ->all();

        $affectedRows = Yii::$app->db->createCommand()
            ->batchInsert('annual_report', [
                'user_id',
                'totalProfit',
            ], $record)->execute();
        if ($affectedRows > 0) {
            $this->stdout('写入累计收益成功,截止日为'.$expireDate);
            return self::EXIT_CODE_NORMAL;
        }

        $this->stdout('写入失败');
    }

    /**
     * 导出指定活动未领取奖励的客户名单
     * 要素：姓名，手机号，剩余奖励次数
     * 本次为植树节活动
     * 希望将来大家能用到
     * @param $promoId
     */
    public function actionTreeData($promoId = 55)
    {
        $sql = "SELECT 
u.real_name '姓名',
u.safeMobile '手机号',
count(*) '剩余浇水次数'
FROM user u 
INNER JOIN promo_lottery_ticket plt
ON u.id = plt.user_id
WHERE 
plt.promo_id = :promoId
AND 
plt.isDrawn = 0
GROUP BY plt.user_id";
        $datas = Yii::$app->db->createCommand($sql, [
            'promoId' => $promoId,
        ])->queryAll();
        if (!empty($datas)) {
            $file = Yii::getAlias('@app/runtime/promo_data_' . date("YmdHis") . '.xlsx');
            $exportData[] = ['姓名', '手机号', '剩余浇水次数'];
            foreach ($datas as $data) {
                array_push($exportData, [
                    $data['姓名'],
                    SecurityUtils::decrypt($data['手机号']),
                    $data['剩余浇水次数'],
                ]);
            }
            //导出Excel
            $objPHPExcel = UserStats::initPhpExcelObject($exportData);
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save($file);
            $this->stdout('操作成功，文件：'. $file . PHP_EOL) ;
        } else {
            $this->stdout('无记录，请重试' . PHP_EOL) ;
        }
    }

    /**
     * 导出有活动奖励未领取的用户名单
     * @param $promoId
     * @param $action
     */
    public function actionUnrewarded($promoId = 55)
    {
        $sql = "SELECT
u.id 'ID', 
u.real_name '姓名',
u.safeMobile '手机号',
count(*) '浇水次数'
FROM user u 
INNER JOIN promo_lottery_ticket plt
ON u.id = plt.user_id
WHERE 
plt.promo_id = :promoId
AND 
plt.isDrawn = 1
GROUP BY plt.user_id";
        $datas = Yii::$app->db->createCommand($sql, [
            'promoId' => $promoId,
        ])->queryAll();
        //判断用户是否有奖励未领取
        //对应次数获得的奖励
        $rewardCount = [
            '80' => 7,
            '60' => 6,
            '28' => 5,
            '8' => 4,
            '5' => 3,
            '3' => 2,
            '2' => 1,
        ];
        $file = Yii::getAlias('@app/runtime/promo_data_' . date("YmdHis") . '.xlsx');
        $exportData[] = ['姓名', '手机号'];
        foreach ($datas as $data) {
            //用户已经领取的奖励次数
            $count = Award::find()
                ->where([
                    'user_id' => $data['ID'],
                    'promo_id' => $promoId,
                ])->count();
            foreach ($rewardCount as $k => $v) {
                //实际浇水次数达到，有部分奖励未领取
                if ($data['浇水次数'] >= $k && $count < $v) {
                    array_push($exportData,[
                        $data['姓名'],
                        SecurityUtils::decrypt($data['手机号']),
                    ]);
                    break;
                }
            }
        }
        //导出Excel
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        $this->stdout('操作成功，文件：'. $file . PHP_EOL);
    }

    /**
     * 导出指定日期内注册并且没有投资的用户名单
     * @param $startDate
     * @param null $endDate
     */
    public function actionExportNoInvestUser($startDate, $endDate = null)
    {
        if (empty($endDate)) {
            $endDate = date('Y-m-d');
        }
        $sql = "SELECT
u.real_name '姓名',
u.safeMobile '联系方式',
u.safeIdCard '身份证号',
u.birthdate '生日',
(DATE_FORMAT(NOW(), '%Y') - SUBSTRING(u.birthdate, 1, 4)) as '年龄',
af.name = '分销商'
FROM user u 
INNER JOIN user_info ui
ON u.id = ui.user_id
LEFT JOIN user_affiliation ua 
on ua.user_id = u.id
LEFT join affiliator af
on ua.affiliator_id = af.id
where ui.investCount = 0
AND date(from_unixtime(u.created_at)) >= :startDate
AND date(from_unixtime(u.created_at)) <= :endDate";
        $datas = Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        $exportData[] = ['姓名', '联系方式', '身份证号', '分销商', '性别', '生日', '年龄'];

        foreach ($datas as $data) {
            $idCard = SecurityUtils::decrypt($data['身份证号']);
            array_push($exportData, [
                $data['姓名'],
                SecurityUtils::decrypt($data['联系方式']),
                empty($idCard) ? null : $idCard,
                empty($data['分销商']) ? '官方' : $data['分销商'],
                !empty($idCard) ? (substr($idCard, -2, 1) % 2 ? '男' : '女') : null,
                $data['生日'],
                $data['年龄'],

            ]);
        }
        //导出Excel
        $file = Yii::getAlias('@app/runtime/no_invest_user_' . date("YmdHis") . '.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        $this->stdout('操作成功，文件：'. $file . PHP_EOL);
    }

    /**
     * 导出指定分销商的用户信息
     * 要素：姓名，联系方式
     * @param $id 对应affiliator表的id
     */
    public function actionExportAffiliatorUser($id)
    {
        //注册成用户
        $sql = "SELECT
u.real_name '姓名',
u.safeMobile '手机号'
FROM user u
INNER JOIN user_affiliation ua 
ON u.id = ua.user_id
WHERE 
ua.affiliator_id = :id";
        $datas = Yii::$app->db->createCommand($sql, [
            'id' => $id,
        ])->queryAll();
        $exportData[] = ['姓名', '联系方式'];
        foreach ($datas as $data) {
            array_push($exportData, [
                $data['姓名'],
                SecurityUtils::decrypt($data['手机号']),
            ]);
        }
        //线下用户
        $sqlOff = "SELECT
u.realName '姓名',
u.mobile '手机号'
FROM offline_user u
INNER JOIN crm_identity ci
on u.crmAccount_id = ci.account_id
WHERE
ci.affiliator_id = :id";
        $datasOff = Yii::$app->db->createCommand($sqlOff, [
            'id' => $id,
        ])->queryAll();
        foreach ($datasOff as $data) {
            array_push($exportData, [
                $data['姓名'],
                $data['手机号'],
            ]);
        }
        //导出Excel
        $file = Yii::getAlias('@app/runtime/channel_user_' . date("YmdHis") . '.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        $this->stdout('操作成功，文件：'. $file . PHP_EOL);
    }

    /**
     * 导出在$startDate至$endDate期间投资成功金额
     * 脚本命令： php yii data/duration-invest-info 2018-03-30 2018-05-01
     *
     * @param string $startDate 投资开始日期
     * @param string $endDate   投资结束日期
     */
    public function actionDurationInvestInfo($startDate, $endDate)
    {
        $investSql = "select
                      uid,sum(order_money) as order_money
                      from online_order
                      where status = 1
                      and date(from_unixtime(created_at)) >= :startDate
                      and date(from_unixtime(created_at)) <= :endDate
                      group by uid
                      order by uid";
        $investInfo = Yii::$app->db->createCommand($investSql, [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ])->queryAll();

        $result = [];
        foreach ($investInfo as $key => $val) {
            $user = User::findOne($val['uid']);
            $result[$key]['user_id'] = $val['uid'];
            $result[$key]['register_time'] = date('Y-m-d H:i:s', $user->created_at);
            $result[$key]['real_name'] = $user->real_name;
            $result[$key]['mobile'] = SecurityUtils::decrypt($user->safeMobile);
            $result[$key]['invest_money'] = $val['order_money'];
        }

        $title = ['用户ID', '注册时间', '姓名', '联系方式', '投资金额'];
        array_unshift($result, $title);
        $file = Yii::getAlias('@app/runtime/duration_invest_info_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($result);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 按照月份导出非等额本息线上年化金额，线下年化金额，等额本息线上年化金额，及年化总金额
     * @param $startDate  开始日期
     * @param $endDate  结束日期
     */
    public function actionDurationAnnualInvest($startDate, $endDate)
    {
        //计算线上非等额本息单月年化投资金额
        $onlineAnnualInvest = OnlineOrder::getOnlineOrdersArray($startDate, $endDate);
        $onlineAnnualInvest = ArrayHelper::index($onlineAnnualInvest, 'orderDate');
        //计算线下非等额本息单月的年化投资金额
        $offlineAnnualInvest = OfflineOrder::getOfflineOrdersArray($startDate, $endDate);
        $offlineAnnualInvest = ArrayHelper::index($offlineAnnualInvest, 'orderDate');
        $annualInvest = [];
        foreach ($onlineAnnualInvest as $key => $value) {
            $startTime = date('Y-m-01', strtotime($key));
            $endTime = date('Y-m-t', strtotime($key));
            $debxOnlineAnnualInvest = Perf::getDebxOnlineAnnualInvest(false, $startTime, $endTime);
            $annualInvest[$key]['date'] = $key;
            $annualInvest[$key]['onlineAnnualInvest'] = $value['annual'];
            $offlineAnnualInvestAmount = isset($offlineAnnualInvest[$key]) ? $offlineAnnualInvest[$key]['annual'] : 0;
            $annualInvest[$key]['offlineAnnualInvest'] = $offlineAnnualInvestAmount;
            $annualInvest[$key]['debxOnlineAnnualInvest'] = $debxOnlineAnnualInvest;
            $annualInvest[$key]['annual'] = Bc::round(bcadd(bcadd($value['annual'], $offlineAnnualInvestAmount), $debxOnlineAnnualInvest), 2) ;
        }
        ksort($annualInvest);
        $title = ['月份', '线上非等额本息年化', '线下非等额本息年化', '线上等额本息年化', '累计年化总金额'];
        array_unshift($annualInvest, $title);
        $file = Yii::getAlias('@app/runtime/duration_annual_invest_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($annualInvest);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }

    /**
     * 导出用户在某活动获得的礼品信息情况
     * 脚本命令：php yii data/export-promo-award 活动ID 导出截止时间（默认昨天）
     * 文件位置：console/runtime/promo_award_*.xlsx
     *
     * @param integer $promoId 活动ID
     * @param null|string $expireDate 导出截止日期【null|YYYY-MM-DD】
     *
     * @return integer
     */
    public function actionExportPromoReward($promoId, $expireDate = null)
    {
        $promoId = (int) $promoId;

        if (null === $expireDate) {
            $expireDate = date('Y-m-d', strtotime('-1 day'));
        }
        if (false === strtotime($expireDate)) {
            $this->stdout('非日期格式参数');
            return self::EXIT_CODE_ERROR;
        }

        $query = new Query();
        $awards = $query->select([
            'u.id as user_id',
            'u.real_name',
            'u.safeMobile',
            'aw.createTime',
            'r.name as rewardName',
        ])->from('award as aw')
            ->innerJoin('user as u', 'u.id = aw.user_id')
            ->innerJoin('reward as r', 'r.id = aw.reward_id')
            ->where(['aw.promo_id' => $promoId])
            ->andFilterWhere(['<=', 'date(aw.createTime)', $expireDate])
            ->orderBy(['aw.createTime' => SORT_DESC])
            ->all();
        foreach ($awards as $k => $award) {
            $awards[$k]['safeMobile'] = SecurityUtils::decrypt($award['safeMobile']);
        }

        $title = ['用户ID', '姓名', '联系方式', '获奖时间', '奖品名称'];
        array_unshift($awards, $title);
        $file = Yii::getAlias('@app/runtime/promo_award_'.date('YmdHis').'.xlsx');
        $objPHPExcel = UserStats::initPhpExcelObject($awards);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }
}
