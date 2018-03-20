<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use backend\modules\product\models\LoanSearch;
use common\controllers\ContractTrait;
use common\lib\bchelp\BcRound;
use common\lib\product\ProductProcessor;
use common\models\adminuser\AdminLog;
use common\models\booking\BookingLog;
use common\models\contract\ContractTemplate;
use common\models\order\EbaoQuan;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\PaymentLog;
use common\models\payment\Repayment;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\promo\PromoService;
use common\models\user\CoinsRecord;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\LoanService;
use common\utils\TxUtils;
use console\command\SqlExportJob;
use P2pl\Borrower;
use Queue\DbQueue;
use Yii;
use yii\data\Pagination;
use yii\db\Query;
use yii\web\Cookie;
use yii\data\ArrayDataProvider;

class ProductonlineController extends BaseController
{
    /**
     * 获取未被软删除的全部融资方信息.
     */
    private function orgUserInfo()
    {
        return User::find()
            ->where(['type' => User::USER_TYPE_ORG])
            ->andWhere(['is_soft_deleted' => 0])
            ->orderBy(['sort' => SORT_DESC])
            ->select('org_name')
            ->indexBy('id')
            ->column();
    }

    /**
     * 获取全部发行商信息.
     */
    private function issuerInfo()
    {
        return Issuer::find()->all();
    }

    /**
     * 添加合同信息.
     */
    private function initContract($pid, array $param)
    {
        $contracts = ContractTemplate::findOne(['pid' => $pid]);

        if (null !== $contracts) {
            ContractTemplate::deleteAll(['pid' => $pid]);
        }

        foreach ($param['title'] as $key => $val) {
            if (!empty($val)) {
                $contract = ContractTemplate::initNew($pid, $val, $param['content'][$key]);
                $contract->save(false);
            }
        }
    }

    /**
     * 合同校验.
     */
    private function validateContract(array $param)
    {
        if (empty(array_filter($param['title']))) {
            throw new \Exception('合同协议至少要输入一份');
        }

        foreach ($param['title'] as $key => $title) {
            if (empty($title)) {
                throw new \Exception('合同名称不能为空');
            }
            if (empty($param['content'][$key])) {
                throw new \Exception('合同内容不能为空');
            }
        }
    }

    /**
     * 标的数据转换.
     */
    private function exchangeValues(OnlineProduct $loan, array $data)
    {
        $loan->finish_date = is_integer($loan->finish_date) ? $loan->finish_date : strtotime($loan->finish_date);
        $loan->start_date = is_integer($loan->start_date) ? $loan->start_date : strtotime($loan->start_date);
        $loan->end_date = is_integer($loan->end_date) ? $loan->end_date : strtotime($loan->end_date);
        $loan->creator_id = $this->getAuthedUser()->id;
        $loan->recommendTime = empty($loan->recommendTime) ? 0 : $loan->recommendTime;
        $loan->is_fdate = isset($data['OnlineProduct']['is_fdate']) ? $data['OnlineProduct']['is_fdate'] : 0;
        $refund_method = (int) $loan->refund_method;
        $loan->tags = trim(str_replace(',', '，', trim($loan->tags)), '，');

        //非测试标，起投金额、递增金额取整
        if (!$loan->isTest) {
            $loan->start_money = intval($loan->start_money);
            $loan->dizeng_money = intval($loan->dizeng_money);
        }

        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI !== $refund_method) {   //还款方式只有到期本息,才设置宽限期
            $loan->kuanxianqi = 0;
        }

        if (!empty($loan->finish_date)) {
            //标的未计息时候，项目期限 = 截止日 - 当前日期 - 1 (页面显示的项目期限都是直接计算，没有使用数据库数据)
            $loan->expires = (new \DateTime(date('Y-m-d', $loan->finish_date)))->diff((new \DateTime(date('Y-m-d'))))->days - 1;
            if ($loan->isAmortized() && !$loan->online_status) {
                $loan->isDailyAccrual = true;
            }
        }

        if (0 === $loan->issuer) {   //当发行方没有选择时,发行方项目编号为空
            $loan->issuerSn = null;
        }
        //当发行方选择不为立合时，底层融资方为空
        if ("深圳立合旺通商业保理有限公司" !== $loan->issuerInfo->name) {
            $loan->originalBorrower = null;
        }

        if (!$loan->isFlexRate) {   //当是否启用浮动利率标志位为false时,清空浮动利率相关数据
            $loan->rateSteps = null;
        }

        if (!$loan->isRedeemable) {
            $loan->redemptionPaymentDates = null;
            $loan->redemptionPeriods = null;
        } else {
            $loan->redemptionPaymentDates = trim($loan->redemptionPaymentDates, ',');
        }

        if (!$loan->isNatureRefundMethod()) {  //当标的还款方式不为按自然时间付息的方式时,固定日期置为null
            $loan->paymentDay = null;
        }

        $loan->isJixiExamined = true;
        if ($refund_method === OnlineProduct::REFUND_METHOD_DEBX) {//等额本息强制不允许转让
            $loan->allowTransfer = false;
        }
        if ($loan->isCustomRepayment) {
            $loan->isJixiExamined = false;//自定义还款需要计息审核
        }

        return $loan;
    }
    //基础信息编辑页面提交时处理宽限期和发行方及发行方编号的数据
    private function exchangeSeniorEditValues(OnlineProduct $loan)
    {
        $refund_method = (int) $loan->refund_method;
        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI !== $refund_method) {   //还款方式只有到期本息,才设置宽限期
            $loan->kuanxianqi = 0;
        }
        if (0 === $loan->issuer) {   //当发行方没有选择时,发行方项目编号为空
            $loan->issuerSn = null;
        }
        return $loan;
    }

    /**
     * 查看标的信息.
     */
    public function actionShow($id)
    {
        $loan = $this->findOr404(OnlineProduct::class, $id);
        $query = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $loan->id]);
        $couponAmount = $query->sum('couponAmount');
        $paymentAmount = $query->sum('paymentAmount');
        $umpResp = Yii::$container->get('ump')->getLoanInfo($loan->id);
        $couponTransfer = $loan->isCouponAmountTransferred();
        $bonusTransfer = $loan->isBonusAmountTransferred();
        $bonusAmount = $loan->getBonusAmount();

        if ($umpResp->isSuccessful()) {
            $balance = bcdiv($umpResp->get('balance'), 100, 2);
        } else {
            $balance = 0;
        }

        return $this->render('show', [
            'loan' => $loan,
            'balance' => $balance,
            'couponAmount' => $couponAmount,
            'paymentAmount' => $paymentAmount,
            'couponTransfer' => $couponTransfer,
            'bonusTransfer' => $bonusTransfer,
            'bonusAmount' => $bonusAmount,
        ]);
    }

    /**
     * 新增标的.
     */
    public function actionAdd()
    {
        $model = OnlineProduct::initNew();
        $model->scenario = 'create';
        $model->allowTransfer = true;
        $con_name_arr = Yii::$app->request->post('name');
        $con_content_arr = Yii::$app->request->post('content');
        $data = Yii::$app->request->post();

        if ($model->load($data) && ($model = $this->exchangeValues($model, $data)) && $model->validate()) {
            try {
                $this->validateContract([
                    'title' => $con_name_arr,
                    'content' => $con_content_arr,
                ]);
            } catch (\Exception $e) {
                $model->addError('contract_type', $e->getMessage());
            }

            if (!$model->hasErrors()) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $model->yield_rate = bcdiv($model->yield_rate, 100, 14);
                    $model->allowedUids = $model->isPrivate ? LoanService::convertUid($model->allowedUids) : null;
                    $model->balance_limit = floatval($data['OnlineProduct']['balance_limit']);
                    $model->save(false);

                    $log = AdminLog::initNew($model);
                    $log->save();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('title', '标的添加异常'.$e->getMessage());
                }

                try {
                    if (!$model->hasErrors()) {
                        $this->initContract($model->id, [
                            'title' => $con_name_arr,
                            'content' => $con_content_arr,
                        ]);

                        $transaction->commit();

                        return $this->redirect(['list']);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('title', '录入合同信息异常');
                }
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'ctmodel' => null,
            'rongziInfo' => $this->orgUserInfo(),
            'con_name_arr' => $con_name_arr,
            'con_content_arr' => $con_content_arr,
            'issuer' => $this->issuerInfo(),
        ]);
    }

    /**
     *引用标的项目
     */
    public function actionQuote($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(OnlineProduct::class, $id);
        $model->scenario = 'create';
        $model->is_fdate = (0 === $model->finish_date) ? 0 : 1;
        $model->yield_rate = bcmul($model->yield_rate, 100, 2);
        $model->allowedUids = $model->mobiles;
        $ctmodel = ContractTemplate::find()->where(['pid' => $id])->all();

        $con_name_arr = Yii::$app->request->post('name');
        $con_content_arr = Yii::$app->request->post('content');
        /*需要保留的属性：cid,refund_method,yield_rate,expires,jiaxi,money,borrow_uid,allowedUids,isPrivate,
        * issuer,issuerSn,filingAmount,start_money,dizeng_money,rateSteps,isFlexRate,paymentDay，allowUseCoupon，
         * isTest，is_xs，tags，isLicai，pointsMultiple，allowTransfer，isCustomRepayment，description
         */
        //清除属性33
        $model->sn = '';
        $model->epayLoanAccountId = '';
        $model->recommendTime = '';
        if ($model->isDailyAccrual) {
            $model->expires = '';
            $model->isDailyAccrual = false;
        }
        $model->fee = '';
        $model->expires_show = '';
        $model->kuanxianqi = '';
        $model->funded_money = '';
        $model->channel = '';
        $model->full_time = '';
        $model->jixi_time = '';
        $model->fk_examin_time = '';
        $model->account_name = '';
        $model->account = '';
        $model->bank = '';
        $model->del_status = '';
        $model->yuqi_faxi = '';
        $model->order_limit = '';
        $model->finish_rate = '';
        $model->is_jixi = '';
        $model->sort = '';
        $model->contract_type = '';
        $model->creator_id = '';
        $model->created_at = '';
        $model->updated_at = '';
        $model->isJixiExamined = '';
        $model->publishTime = '';
        $model->id = '';
        $model->title = '';
        $model->internalTitle = '';
        $model->start_date = '';
        $model->end_date = '';
        $model->finish_date = '';
        $model->online_status = '';
        $model->status = '';
        $model->is_fdate = '';

        return $this->render('edit', [
            'model' => $model,
            'ctmodel' => $ctmodel,
            'rongziInfo' => $this->orgUserInfo(),
            'con_name_arr' => $con_name_arr,
            'con_content_arr' => $con_content_arr,
            'issuer' => $this->issuerInfo(),
        ]);
    }

    /**
     * 编辑标的项目.
     */
    public function actionEdit($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $model = $this->findOr404(OnlineProduct::class, $id);

        $model->scenario = 'create';
        $model->is_fdate = (0 === $model->finish_date) ? 0 : 1;
        $model->yield_rate = bcmul($model->yield_rate, 100, 2);
        $model->allowedUids = $model->mobiles;   //获取user中电话列表
        $ctmodel = ContractTemplate::find()->where(['pid' => $id])->all();

        $con_name_arr = Yii::$app->request->post('name');
        $con_content_arr = Yii::$app->request->post('content');
        $data = Yii::$app->request->post();

        if ($model->load($data) && ($model = $this->exchangeValues($model, $data)) && $model->validate()) {
            try {
                $this->validateContract([
                    'title' => $con_name_arr,
                    'content' => $con_content_arr,
                ]);
            } catch (\Exception $e) {
                $model->addError('contract_type', $e->getMessage());
            }

            if (!$model->hasErrors()) {
                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $model->yield_rate = bcdiv($model->yield_rate, 100, 14);
                    $model->allowedUids = $model->isPrivate ? LoanService::convertUid($model->allowedUids) : null;
                    $model->balance_limit = floatval($data['OnlineProduct']['balance_limit']);
                    $model->save(false);

                    $log = AdminLog::initNew($model);
                    $log->save();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('title', '标的添加异常'.$e->getMessage());
                }

                try {
                    if (!$model->hasErrors()) {
                        $this->initContract($model->id, [
                            'title' => $con_name_arr,
                            'content' => $con_content_arr,
                        ]);

                        $transaction->commit();

                        return $this->redirect(['list']);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('title', '录入合同信息异常');
                }
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'ctmodel' => $ctmodel,
            'rongziInfo' => $this->orgUserInfo(),
            'con_name_arr' => $con_name_arr,
            'con_content_arr' => $con_content_arr,
            'issuer' => $this->issuerInfo(),
        ]);
    }
    //高级编辑页面（有权限的操作人员在标的的整个过程中均可编辑保存）
    public function actionSeniorEdit($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }
        $model = $this->findOr404(OnlineProduct::class, $id);
        $model->scenario = 'senior_edit';
        $data = Yii::$app->request->post();
        if ($model->load($data) && ($model = $this->exchangeSeniorEditValues($model)) && $model->validate()) {
            if (!$model->hasErrors()) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);
                    $log = AdminLog::initNew($model);
                    $log->save();
                    $transaction->commit();
                    return $this->redirect(['list']);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    $model->addError('title', '标的添加异常'.$e->getMessage());
                }
            }
        }

        return $this->render('senior_edit', [
            'model' => $model,
            'issuer' => $this->issuerInfo(),
        ]);
    }

    /**
     * 上线操作.
     */
    public function actionLineon()
    {
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }
        $ids = Yii::$app->request->post('pids');
        $loans = (new \yii\db\Query())
                ->select('loan.*,eu.epayUserId')
                ->from(['online_product loan'])
                ->innerJoin('epayuser eu', 'loan.borrow_uid=eu.appUserId')
                ->where('loan.id in ('.$ids.')')->all();

        $error_loans = '';
        foreach ($loans as $loan) {
            $borrow = new Borrower($loan['epayUserId'], null, Borrower::MERCHAT);//借款人测试阶段只能用7601209
            unset($loan['epayUserId']);
            $loanObj = new OnlineProduct($loan);
            try {
                $resp = OnlineProduct::createLoan($loanObj, $borrow);
                $updateData = [
                    'epayLoanAccountId' => $resp,
                    'online_status' => 1,
                    'publishTime' => date('Y-m-d H:i:s'),
                ];
                //添加标的更改记录
                $log = AdminLog::initNew(['tableName' => OnlineProduct::tableName(), 'primaryKey' => $loan['id']], Yii::$app->user, $updateData);
                $log->save();
                OnlineProduct::updateAll($updateData, 'id=' . $loan['id']);
                LoanService::updateLoanState($loanObj, OnlineProduct::STATUS_PRE);
            } catch (\Exception $ex) {
                $error_loans .= $loanObj->sn.$ex->getMessage().',';
            }
        }
        if ('' !== $error_loans) {
            $error_loans = ',请注意,如下标的联动一侧上线失败'.substr($error_loans, 0, -1);
        }
        return ['result' => 1, 'message' => '操作已完成'.$error_loans];
    }

    public function actionProductinfo($sn = null)
    {
        $model = $sn ? OnlineProduct::findOne(['sn' => $sn]) : new OnlineProduct();
        if ($model == null) {
            $model = new OnlineProduct();
        }

        return json_encode(array(
            'title' => $model->title,
            'yield_rate' => $model->yield_rate,
            'product_duration' => $model->product_duration,
        ));
    }

    /**
     * 批量删除标的.(暂时未用到)
     */
    public function actionDelmore($ids)
    {
        if (empty($ids)) {
            throw $this->ex404();     //参数无效,抛出404异常
        }

        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $_model = OnlineProduct::findOne($id);
            if (null === $_model) {    //如果没有找到标的信息,跳过本次循环,继续下次循环操作
                continue;
            }
            $_model->del_status = 1;
            $_model->scenario = 'del';
            $_model->save();
        }

        echo json_encode(array('res' => 1));
    }

    /**
     * 撤标.
     */
    public function actionCancel($id)
    {
        if (empty($id)) {
            return ['res' => 0, 'msg' => 'id为空'];
        }
        $online_product = OnlineProduct::findOne($id);
        if (null === $online_product) {
            return ['res' => 0, 'msg' => '项目信息不存在'];    //当对象信息为空时,抛出错误信息
        }
        if ($online_product->status == OnlineProduct::STATUS_PRE) {
            return ['res' => 0, 'msg' => '项目处于预告期'];
        }
        if ($online_product->status == OnlineProduct::STATUS_LIU) {
            return ['res' => 0, 'msg' => '项目已经流标'];
        }
        if ($online_product->status == OnlineProduct::STATUS_FULL) {
            return ['res' => 0, 'msg' => '项目已经满标'];
        }
        if ($online_product->status > OnlineProduct::STATUS_LIU) {
            return ['res' => 0, 'msg' => '项目已经成立'];
        }
        $order = new OnlineOrder();
        $res = $order->cancelOnlinePro($id);
        $msg = $res ? '撤标成功' : '撤标失败';

        return ['res' => $res, 'msg' => $msg];
    }

    /**
     * 贷款管理->项目列表页.
     */
    public function actionList()
    {
        $this->layout="@app/views/layouts/frame_productline_list";
        $loanStatus = Yii::$app->params['deal_status'];
        $loanTable = OnlineProduct::tableName();
        $loanSearch = new LoanSearch();
        $loanSearch->load(Yii::$app->request->get(), '');
        $loanSearch->isTest = !is_null($loanSearch->isTest) ? $loanSearch->isTest : Yii::$app->request->cookies->getValue('loanListFilterIsTest', 0);
        if ($loanSearch->isTest) {
            Yii::$app->response->cookies->add(new Cookie(['name' => 'loanListFilterIsTest', 'value' => 1, 'expire' => strtotime('next year'), 'httpOnly' => false]));
        }

        /**
         * @var Query $query
         */
        $query = $loanSearch->search();
        $query->select("$loanTable.*")
            ->addSelect(['xs_status' => "if($loanTable.`is_xs` = 1 && $loanTable.`status` < 3, 1, 0)"])
            ->addSelect(['isrecommended' => "if($loanTable.`online_status`=1 && $loanTable.`isPrivate`=0, $loanTable.`recommendTime`, 0)"])
            ->addSelect(['effect_jixi_time' => "if($loanTable.`is_jixi`=1, $loanTable.`jixi_time`, 0)"])
            ->addSelect(['product_status' => "(case $loanTable.`status` when 4 then 7 when 7 then 4 else $loanTable.`status` end)"]);
        $totalCount = $query->count();
        //收益中的标的能够按照满标时间降序排列（最新时间在最前面）
        if ($loanSearch->status === 5) {
            $query->orderBy("full_time desc, xs_status desc, isrecommended desc, online_status asc, product_status asc,effect_jixi_time desc, sn desc");
        } else {
            $query->orderBy("xs_status desc, isrecommended desc, online_status asc, product_status asc,effect_jixi_time desc, sn desc");

        }
        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => '20']);
        $models = $query->offset($pages->offset)->limit($pages->limit)->all();
        $userAuthSeniorEdit = intval(OnlineProduct::hasAuthSeniorEdit());
        return $this->render('list', [
            'models' => $models,
            'pages' => $pages,
            'loanStatus' => $loanStatus,
            'loanSearch' => $loanSearch,
            'userAuthSeniorEdit' => $userAuthSeniorEdit
        ]);
    }

    /**
     * 标的相关信息导出
     */
    public function actionExport($exportType)
    {
        if (in_array($exportType, ['loan_invest_data', 'user_invest_data'])) {
            $loanSearch = new LoanSearch();
            $loanSearch->load(Yii::$app->request->get(), '');
            /**
             * @var Query $query
             * @var DbQueue $dbQueue
             */
            $query = $loanSearch->search();
            $dbQueue = Yii::$container->get('db_queue');
            $sn = TxUtils::generateSn('Export');

            if ($exportType === 'loan_invest_data') {
                $loanTable = OnlineProduct::tableName();
                $orderTable = OnlineOrder::tableName();
                if (!$loanSearch->hasInnerJoinOrder) {
                    $query->innerJoin("$orderTable", "$orderTable.online_pid = $loanTable.id");
                    $query->groupBy("$loanTable.id");
                    $loanSearch->hasInnerJoinOrder = true;
                }
                $sql = $query
                    ->select(["$loanTable.title", "$loanTable.internalTitle"])
                    ->addSelect(["loanStatus" => "(CASE $loanTable.status WHEN 2 THEN '募集中' WHEN 3 THEN '满标' WHEN 5 THEN '还款中' WHEN 6 THEN '已还清' WHEN 7 THEN '提前结束' END)"])
                    ->addSelect(["isXs" => "IF($loanTable.is_xs, '新手标', '非新手标')"])
                    ->addSelect(["isJiXi" => "IF($loanTable.is_jixi, '已计息', '未计息')"])
                    ->addSelect(["expires" => "IF($loanTable.refund_method = 1,CONCAT(IF($loanTable.finish_date > 0 && ! $loanTable.is_jixi,ABS(DATEDIFF(DATE(FROM_UNIXTIME($loanTable.finish_date)), DATE(NOW()))), $loanTable.expires ),'天'),CONCAT($loanTable.expires, '月'))"])
                    ->addSelect(["jiXiDate" => " DATE( IF($loanTable.is_jixi,FROM_UNIXTIME($loanTable.jixi_time),''))"])
                    ->addSelect(["finishDate" => "DATE(IF($loanTable.finish_date > 0, FROM_UNIXTIME($loanTable.finish_date),''))"])
                    ->addSelect(["orderMoney" => "sum($orderTable.order_money)"])
                    ->addSelect(["userCount" => "count(distinct $orderTable.uid)"])
                    ->addSelect(["rate" => "CONCAT(FORMAT($loanTable.yield_rate * 100,2), IF($loanTable.isFlexRate && INSTR(REVERSE($loanTable.rateSteps), ',') > 0, CONCAT('-', REVERSE(SUBSTRING(REVERSE($loanTable.rateSteps), 1, INSTR(REVERSE($loanTable.rateSteps), ',') - 1 ) ) ), '' ) , '%')"])
                    ->andWhere(["$orderTable.status" => 1])
                    ->orderBy(["$loanTable.id" => SORT_ASC])
                    ->createCommand()
                    ->getRawSql();
                //导出标的的投资信息
                $job = new SqlExportJob([
                    'sql' => $sql,
                    'queryParams' => null,
                    'exportSn' => $sn,
                    'itemLabels' => ['标的名称', '标的副标题', '标的状态', '是否是新手标', '是否计息', '产品期限', '起息日', '到期日', '投资金额', '投资用户数', '项目利率'],
                    'itemType' => ['string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'float', 'int', 'string'],
                ]);
                if ($dbQueue->pub($job)) {
                    return $this->redirect('/growth/export/result?sn=' . $sn . '&key=&title=导出标的的投资信息');
                }

            } elseif ($exportType === 'user_invest_data') {
                $loanIds = $query->select("online_product.id")->column();
                if (empty($loanIds)) {
                    echo '没有找到符合条件的标的';
                    return false;
                }
                $sql = "SELECT
    p.title,
    p.internalTitle,
    CASE p.status WHEN 2 THEN '募集中' WHEN 3 THEN '满标' WHEN 5 THEN '还款中' WHEN 6 THEN '已还清' WHEN 7 THEN '提前结束' END as loanStatus,
    IF(p.is_xs, '新手标', '非新手标') AS is_xs,
    IF(p.is_jixi, '已计息', '未计息') AS is_jixi,
    IF(
        p.refund_method = 1,
        CONCAT(
            IF(
                p.finish_date > 0 && ! p.is_jixi,
                ABS(
                    DATEDIFF(
                        DATE(FROM_UNIXTIME(p.finish_date)),
                        DATE(NOW())
                    )
                ),
                p.expires
            ),
            '天'
        ),
        CONCAT(p.expires, '月')
    ) AS expires,
    DATE(
        IF(
            p.is_jixi,
            FROM_UNIXTIME(p.jixi_time),
            ''
        )
    ) as jixi_date,
    DATE(
        IF(
            p.finish_date > 0,
            FROM_UNIXTIME(p.finish_date),
            ''
        )
    ) as finishDate,
    o.uid,
    u.real_name,
    FROM_UNIXTIME(o.order_time),
    o.order_money,
    o.yield_rate
FROM
    online_order AS o
INNER JOIN 
    online_product AS p
ON
    p.id = o.online_pid
INNER JOIN 
    `user` AS u
ON
    o.uid = u.id
WHERE
    o.status = 1 
    and u.type = 1
    and p.id in (" . implode(',', $loanIds) . ")
ORDER BY p.id ASC,u.id ASC,o.id ASC";

                //导出投资过指定标的的所有用户的投资记录
                $job = new SqlExportJob([
                    'sql' => $sql,
                    'queryParams' => null,
                    'exportSn' => $sn,
                    'itemLabels' => ['标的名称', '标的副标题', '标的状态', '是否是新手标', '是否计息', '产品期限', '起息日', '到期日', '用户ID', '用户姓名', '投资时间', '投资金额', '实际利率'],
                    'itemType' => ['string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'string', 'float', 'float'],
                ]);
                if ($dbQueue->pub($job)) {
                    return $this->redirect('/growth/export/result?sn=' . $sn . '&key=&title=导出投资过指定标的的所有用户的投资记录');
                }
            }
        } else {
            return $this->redirect('/product/productonline/list');
        }

    }

    /**
     * 获取待还款统计数量.
     * 1. 统计7日内待还款数量;
     * 2. 统计当日待还款数量;
     */
    public function actionHkStatsCount()
    {
        return ['week' => count($this->HkStats(7)), 'today' => count($this->HkStats(1))];  //返回包含所有统计信息的数量数组
    }

    /**
     * 1. 计算截止日,自今天起往后延$days天;
     * 2. 统计自截止日之前的所有待还款项目;
     */
    private function HkStats($days)
    {
        if (!is_integer($days)) {
            throw new \Exception();
        }

        $op = OnlineProduct::tableName();
        $r = Repayment::tableName();

        $query = Repayment::find()
            ->innerJoin($op, "$r.loan_id = $op.id");

        $endDay = date('Y-m-d', strtotime("+$days days"));    //所有区段都要统计自截止日之前的所有待还款项目
        $query->where(['<', 'dueDate', $endDay]);

        $model = $query->andWhere(['isRefunded' => 0, "$op.status" => OnlineProduct::STATUS_HUAN, "$op.isTest" => 0])->select(['loan_id'])   //只统计规定时间内的状态为还款中的标的
            ->asArray()
            ->all();

        return array_unique(array_column($model, 'loan_id'));
    }

    /**
     * 标的删除.
     */
    public function actionDel()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $model = OnlineProduct::findOne($id);
            $model->scenario = 'del';
            $model->del_status = 1;
            //修改标的修改记录
            try {
                $log = AdminLog::initNew($model);
                $log->save();
            } catch (\Exception $e) {
                return [
                    'result' => 0,
                    'message' => '标的日志记录失败',
                ];
            }
            if ($model->save()) {
                return ['code' => 1, 'message' => '删除成功'];
            }
        }

        return ['code' => 0, 'message' => '删除失败'];
    }

    /**
     * 项目提前成立.
     */
    public function actionFound()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $model = OnlineProduct::findOne($id);
            if (empty($model) || $model->status != OnlineProduct::STATUS_NOW) {
                return ['result' => '0', 'message' => '无法找到该项目,或者项目状态不是募集中'];
            } else {
                $bc = new BcRound();
                $transaction = Yii::$app->db->beginTransaction();
                //提前成立的标的募集完成率应为100%，即1.0000
                $updateData = [
                    'status' => OnlineProduct::STATUS_FOUND,
                    'sort' => OnlineProduct::SORT_FOUND,
                    'full_time' => time(),
                    'finish_rate' => 1.0000,
                ];
                //修改标的修改记录
                try {
                    $log = AdminLog::initNew($model, Yii::$app->user, $updateData);
                    $log->save();
                } catch (\Exception $e) {
                    return [
                        'result' => 0,
                        'message' => '标的日志记录失败',
                    ];
                }
                $up_srs = OnlineProduct::updateAll($updateData, ['id' => $id]);
                if (!$up_srs) {
                    $transaction->rollBack();

                    return ['result' => '0', 'message' => '操作失败,状态更新失败,请联系技术'];
                }
                $orders = OnlineOrder::getOrderListByCond(['online_pid' => $id, 'status' => OnlineOrder::STATUS_SUCCESS]);
                foreach ($orders as $ord) {
                    $ua = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $ord['uid']]);
                    $ua->investment_balance = $bc->bcround(bcadd($ua->investment_balance, $ord['order_money']), 2);
                    $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $ord['paymentAmount']), 2);//冻结金额减去实付金额
                    $mrmodel = new MoneyRecord();
                    $mrmodel->account_id = $ua->id;
                    $mrmodel->sn = TxUtils::generateSn('MR');
                    $mrmodel->type = MoneyRecord::TYPE_FULL_TX;
                    $mrmodel->osn = $ord['sn'];
                    $mrmodel->uid = $ord['uid'];
                    $mrmodel->balance = $ua->available_balance;
                    $mrmodel->in_money = $ord['order_money'];
                    $mrmodel->remark = '项目成立,冻结金额转入理财金额账户。交易金额'.$ord['order_money'];
                    if (!$ua->save() || !$mrmodel->save()) {
                        $transaction->rollBack();

                        return ['result' => '0', 'message' => '操作失败,账户更新失败,请联系技术'];
                    }
                }

                if (!empty($model->recommendTime)) {
                    $count = OnlineProduct::find()
                        ->where('recommendTime != 0')
                        ->andWhere([
                            'isPrivate' => 0,
                            'del_status' => 0,
                        ])
                        ->count();

                    if ($count > 1) {
                        $model->recommendTime = 0;
                        if (!$model->save(false)) {
                            $transaction->rollBack();

                            return ['code' => 0, 'message' => '操作失败'];
                        }
                    }
                }

                $transaction->commit();

                return ['result' => '1', 'message' => '操作成功'];
            }
        } else {
            return ['result' => '0', 'message' => 'ID不能为空'];
        }
    }

    /**
     * 确认计息.
     */
    public function actionJixicorfirm()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $loan = OnlineProduct::findOne($id);
            if (null === $loan
                || !in_array($loan->status, [OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND])
                || empty($loan->jixi_time)
                || $loan->is_jixi
            ) {
                return ['result' => '0', 'message' => '无法找到该项目,或者项目现阶段不允许开始计息'];
            }
            if ($loan->isCustomRepayment && !$loan->isJixiExamined) {
                return ['result' => '0', 'message' => '自定义还款必须进行计息审核操作'];
            }

            $res = OnlineRepaymentPlan::generatePlan($loan);
            if ($res) {
                //确认计息之后同步用户资产
                try {
                    $orders = $this->initUserAssets($loan);
                } catch (\Exception $e) {
                    $message = $e->getMessage();

                    Yii::trace('项目成立异常,项目ID为'.$loan->id.
                        ',错误信息为'.$message.
                        ',操作时间为'.date('Y-m-d H:i:s').
                        ',操作人为'.$this->getAuthedUser()->username);

                    return ['result' => '0', 'message' => $message];
                }

                //确认计息之后给用户赠送积分
                try {
                    PromoService::doAfterLoanJixi($loan);
                } catch (\Exception $ex) {

                }

                //确认计息之后更新用户的累计年化投资金额,新手专享,理财计划,以及转让订单除外
                $this->incrAnnualInvestment($orders, $loan);

                return ['result' => '1', 'message' => '操作成功'];
            }

            return ['result' => '0', 'message' => '操作失败，请联系技术人员'];
        }

        return ['result' => '0', 'message' => 'ID不能为空'];
    }

    /**
     * 计息审核
     *
     * @param $id
     * @return string
     */
    public function actionJixiExamined($id)
    {
        /**
         * @var OnlineProduct $loan
         */
        $loan = $this->findOr404(OnlineProduct::className(), [
            'id' => $id,
            'isJixiExamined' => false,
        ]);
        if (Yii::$app->request->isPost) {
            $loan->isJixiExamined = true;
            $loan->save(false);
            return $this->redirect('/product/productonline/list');
        }
        $orders = OnlineOrder::find()->where(['status' => OnlineOrder::STATUS_SUCCESS, 'online_pid' => $loan->id])->all();
        $repayment = [];
        foreach ($orders as $order) {
            /**
             * @var OnlineOrder $order
             */
            $data = OnlineRepaymentPlan::calcBenxi($order);
            foreach ($data as $key => $value) {
                $term = $key + 1;
                $repayment[$term]['repaymentDate'] = $value['date'];
                $repayment[$term]['term'] = $term;
                $repayment[$term]['totalPrincipal'] = isset($repayment[$term]['totalPrincipal']) ? $repayment[$term]['totalPrincipal'] : 0;
                $repayment[$term]['totalInterest'] = isset($repayment[$term]['totalInterest']) ? $repayment[$term]['totalInterest'] : 0;
                $repayment[$term]['totalPrincipal'] = bcadd($repayment[$term]['totalPrincipal'], $value['principal'], 2);
                $repayment[$term]['totalInterest'] = bcadd($repayment[$term]['totalInterest'], $value['interest'], 2);
                $repayment[$term]['orderData'][] = [
                    'userId' => $order->uid,
                    'orderMoney' => $order->order_money,
                    'rate' => $order->yield_rate,
                    'principal' => $value['principal'],
                    'interest' => $value['interest'],
                ];
            }
        }
        return $this->render('jixi_examined', [
            'loan' => $loan,
            'repayment' => $repayment,
        ]);
    }

    /**
     * 更新用户累计年化投资金额.
     *
     * 1. 新手专享，理财计划，转让产品除外;
     * 2. 财富值有变动的时候，记录财富值流水;
     */
    private function incrAnnualInvestment(array $orders, OnlineProduct $loan)
    {
        if (!$loan->is_xs && !$loan->isLicai && !empty($orders)) {    //新手标、转让,以及理财计划标的不计算在内
            foreach ($orders as $order) {
                $originalCoins = $order->user->coins;
                $res = Yii::$app->db->createCommand('update user set annualInvestment = annualInvestment + '.$order->annualInvestment.' where id = '.$order->user->id)->execute();

                if ($res) {
                    $user = User::findOne($order->user->id);
                    $currentCoins = $user->coins;

                    if ($originalCoins !== $currentCoins) {
                        $coins = new CoinsRecord([
                            'user_id' => $order->user->id,
                            'order_id' => $order->id,
                            'incrCoins' => bcsub($currentCoins, $originalCoins, 0),
                            'finalCoins' => $currentCoins,
                            'createTime' => date('Y-m-d H:i:s'),
                        ]);

                        $coins->save();
                    }
                }
            }
        }
    }

    private function initUserAssets(OnlineProduct $loan)
    {
        $orders = OnlineOrder::findAll(['online_pid' => $loan->id, 'status'  => OnlineOrder::STATUS_SUCCESS]);

        if (empty($orders)) {
            throw new \Exception('请求数据不能为空');
        }

        foreach ($orders as $order) {
            $reqData[] = [
                'user_id' => $order->uid,
                'order_id' => $order->id,
                'loan_id' => $order->online_pid,
                'amount' => $order->order_money * 100,
                'orderTime' => date('Y-m-d H:i:s', $order->created_at),
                'isTest' => $loan->isTest,
                'allowTransfer' => $loan->allowTransfer(),
            ];
        }

        $txClient = \Yii::$container->get('txClient');
        $respData = $txClient->post('assets/record', $reqData);

        if (count($respData) !== count($reqData)) {
            throw new \Exception('请求记录条数与返回记录条数不符');
        }

        foreach ($reqData as $key => $data) {   //二次比较返回结果与请求结果
            if (!empty(array_diff_assoc($data, $respData[$key]))) {
                throw new \Exception('请求记录内容与返回记录内容不符');
            }
        }

        return $orders;
    }

    /**
     * 设置计息时间.
     */
    public function actionJixi($product_id)
    {
        if (empty($product_id)) {
            throw $this->ex404();   //当参数无效时,抛出404异常
        }

        $c_flag = 0;
        $model = OnlineProduct::findOne($product_id);
        if (null === $model) {
            throw $this->ex404();
        }

        $err = '';
        $model->scenario = 'jixi';
        if ($model->is_jixi === 1) {
            $err = '已经开始计息，不允许修改计息开始时间';
        } elseif ($model->load(Yii::$app->request->post())) {
            $model->jixi_time = strtotime($model->jixi_time);
            $full_time = strtotime(date('Y-m-d', $model->full_time));
            $finish_date = strtotime(date('Y-m-d', $model->finish_date));
            if ($model->status == OnlineProduct::STATUS_FULL && $model->jixi_time <= $full_time) {
                $err = '计息开始时间必须大于项目满标时间 '.date('Y-m-d', $model->full_time);
            } elseif ($model->status == OnlineProduct::STATUS_FOUND && $model->jixi_time <= $full_time) {
                $err = '计息开始时间必须大于项目提前募集结束时间 '.date('Y-m-d', $model->full_time);
            } elseif (!empty($model->finish_date) && $model->jixi_time >= $finish_date) {
                $err = '计息开始时间必须小于项目的截止时间 '.date('Y-m-d', $model->finish_date);
            }

            if (!empty($err)) {
                $model->addError('jixi_time', $err);
            } else {
                try {
                    $log = AdminLog::initNew($model);
                    $log->save();
                } catch (\Exception $e) {
                    $model->addError('jixi_time', '标的日志错误');
                }
                $model->save();
                $c_flag = 'close';
            }
        }

        if (!empty($model->jixi_time)) {
            $model->jixi_time = date('Y-m-d', $model->jixi_time);
        } else {
            $model->jixi_time = '';
        }

        $this->layout = false;

        return $this->render('jixi', ['model' => $model, 'c_flag' => $c_flag]);
    }

    /**
     * 股权投资列表页.
     */
    public function actionBookinglist()
    {
        $name = Yii::$app->request->get('name');
        $data = BookingLog::find();

        if (!empty($name)) {
            $data->andFilterWhere(['like', 'name', $name]);
        }

        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '20']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('bookinglist', [
            'model' => $model,
            'pages' => $pages,
            'name' => $name,
        ]);
    }

    /**
     * 标的推荐功能.
     */
    public function actionRecommend()
    {
        $dealId = Yii::$app->request->get('id');

        $deal = OnlineProduct::findOne($dealId);
        if (!$deal) {
            throw $this->ex404();
        }

        if (!empty($deal->recommendTime)) {
            $count = OnlineProduct::find()->where('recommendTime != 0')->andWhere(['isPrivate' => 0, 'del_status' => 0])->count();

            if ($count <= 1) {
                return ['code' => 0, 'message' => '推荐标的至少要有一个'];
            }

            $deal->recommendTime = 0;
        } else {
            $deal->recommendTime = time();
        }
        //记录标的日志
        try {
            $log = AdminLog::initNew($deal);
            $log->save();
        } catch (\Exception $e) {
            return ['code' => 0, 'message' => '标的日志记录失败'];
        }
        if (!$deal->save(false)) {
            return ['code' => 0, 'message' => '操作失败'];
        }

        return ['code' => 1, 'message' => '操作成功'];
    }

    /**
     * 转让列表一级页面,发起转让
     */
    public function actionSponsoredtransfer($page = 1)
    {
        $notes = [];
        $totalCount = 0;
        $pageSize = 10;

        $txClient = Yii::$container->get('txClient');
        $response = $txClient->post('credit-note/list', ['page' => $page, 'page_size' => $pageSize, 'sort' => '-createTime']);
        
        if (null !== $response) {
            $notes = $response['data'];
            $totalCount = $response['total_count'];
            $pageSize = $response['page_size'];

            foreach ($notes as $key => $note) {
                $notes[$key]['user'] = User::findOne($note['user_id']);
                $notes[$key]['loan'] = OnlineProduct::findOne($note['loan_id']);
            }
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $notes,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
        return $this->render('transferlist', ['dataProvider' => $dataProvider, 'pages' => $pages]);
    }

    /**
     * 转让列表二级页面,购买转让
     */
    public function actionBuytransfer($page = 1)
    {
        $note_id = Yii::$app->request->get('loan_id');
        $notes = [];
        $totalCount = 0;
        $pageSize = 10;

        $response = Yii::$container->get('txClient')->get('credit-order/list', [
            'id' => $note_id,
            'page' => $page,
            'page_size' => $pageSize,
        ]);

        if (null !== $response) {
            $notes = $response['data'];
            $totalCount = $response['totalCount'];
            $pageSize = $response['pageSize'];

            foreach ($notes as $key => $note) {
                $userinfo = User::findOne($note['user_id']);
                $notes[$key]['user'] = $userinfo;
            }
        }
        foreach ($notes as $key => $creditOrder) {
            $notes[$key]['baoquan'] = EbaoQuan::find()->where([
                'uid' => $creditOrder['user_id'],
                'itemType' => EbaoQuan::ITEM_TYPE_CREDIT_ORDER,
                'itemId' => $creditOrder['id'],
                'success' => 1,
            ])->all();
        }

        $dataProvider = new ArrayDataProvider([
            'allModels' => $notes,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize]);
        return $this->render('buytransfer', ['dataProvider' => $dataProvider, 'pages' => $pages]);
    }

    /**
     * 隐藏/还原标的.
     *
     * 1. 修改对应标的的del_status字段;
     * 2. 权限控制;
     * 3. 实际募集金额为零且标的状态为募集提前结束时,才允许修改;
     */
    public function actionHideLoan($id)
    {
        if (empty($id)) {
            throw $this->ex404();
        }

        $loan = $this->findOr404(OnlineProduct::class, $id);
        $msg = null;

        if (1 === bccomp($loan->funded_money, 0, 2)) {
            $msg = '当前标的实际募集金额不为0，无法修改！';
        } elseif (OnlineProduct::STATUS_FOUND !== $loan->status) {
            $msg = '当前标的状态不为募集提前结束，无法修改！';
        }

        if ($msg) {
            return [
                'code' => 1,
                'message' => $msg,
            ];
        }

        $loan->del_status = !$loan->del_status;
        $res = $loan->save(false);
        AdminLog::initNew($loan)->save(false);

        return [
            'code' => intval(!res),
            'message' => $res ? '操作成功' : '操作失败',
        ];
    }
}
