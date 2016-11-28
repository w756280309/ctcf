<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\lib\product\ProductProcessor;
use common\models\adminuser\AdminLog;
use common\models\booking\BookingLog;
use common\models\contract\ContractTemplate;
use common\models\order\BaoQuanQueue;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\Repayment;
use common\models\product\Issuer;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\service\LoanService;
use common\utils\TxUtils;
use P2pl\Borrower;
use Yii;
use yii\data\Pagination;
use yii\web\Cookie;

class ProductonlineController extends BaseController
{
    /**
     * 获取全部融资方信息.
     */
    private function orgUserInfo()
    {
        return User::find()
            ->where(['type' => User::USER_TYPE_ORG])
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
            $contract = ContractTemplate::initNew($pid, $val, $param['content'][$key]);
            $contract->save(false);
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

        if (false === strpos($param['title'][0], '认购协议')) {
            throw new \Exception('合同名称错误,第一份合同应该录入认购协议');
        }

        if (false === strpos($param['title'][1], '风险揭示书')) {
            throw new \Exception('合同名称错误,第二份合同应该录入风险揭示书');
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

        //非测试标，起投金额、递增金额取整
        if (!$loan->isTest) {
            $loan->start_money = intval($loan->start_money);
            $loan->dizeng_money = intval($loan->dizeng_money);
        }

        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI !== $refund_method) {   //还款方式只有到期本息,才设置项目截止日和宽限期
            $loan->finish_date = 0;
            $loan->kuanxianqi = 0;
        }

        if (!empty($loan->finish_date) && OnlineProduct::REFUND_METHOD_DAOQIBENXI === $refund_method) {
            //若截止日期不为空，重新计算项目天数
            $pp = new ProductProcessor();
            $loan->expires = $pp->LoanTimes(date('Y-m-d H:i:s', $loan->start_date), null, $loan->finish_date, 'd', true)['days'][1]['period']['days'];
        }

        if (0 === $loan->issuer) {   //当发行方没有选择时,发行方项目编号为空
            $loan->issuerSn = null;
        }

        if (!$loan->isFlexRate) {   //当是否启用浮动利率标志位为false时,清空浮动利率相关数据
            $loan->rateSteps = null;
        }

        if (!$loan->isNatureRefundMethod()) {  //当标的还款方式不为按自然时间付息的方式时,固定日期置为null
            $loan->paymentDay = null;
        }

        return $loan;
    }

    /**
     * 新增标的.
     */
    public function actionAdd()
    {
        $model = OnlineProduct::initNew();
        $model->scenario = 'create';

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
        $model->allowedUids = $model->mobiles;
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
                ->innerJoin('EpayUser eu', 'loan.borrow_uid=eu.appUserId')
                ->where('loan.id in ('.$ids.')')->all();

        $error_loans = '';
        foreach ($loans as $loan) {
            $borrow = new Borrower($loan['epayUserId'], null, Borrower::MERCHAT);//借款人测试阶段只能用7601209
            unset($loan['epayUserId']);
            $loanObj = new OnlineProduct($loan);
            try {
                $resp = OnlineProduct::createLoan($loanObj, $borrow);
                $updateData = ['epayLoanAccountId' => $resp, 'online_status' => 1];
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
     * 删除.（未使用）
     *
     * @param type $id
     * @param type $page
     *
     * @return type
     */
    public function actionDelete()
    {
        if (!Yii::$app->request->isPost) {
            return [
                'result' => 0,
                'message' => '非法请求',
            ];
        }
        $id = Yii::$app->request->post('id');
        $model = $id ? OnlineProduct::findOne($id) : new OnlineProduct();
        if ($model->status > 1) {
            return [
                'result' => 0,
                'message' => '不允许删除',
            ];
        }
        $model->del_status = 1;
        $model->scenario = 'del';
        $model->save();

        return ['result' => 1, 'message' => '成功'];
    }

    public function actionDelmore($ids = null)
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
     *
     * @param type $id
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
        $status = Yii::$app->params['deal_status'];
        $request = Yii::$app->request->get();

        $op = OnlineProduct::tableName();
        $data = OnlineProduct::find()
            ->select("$op.*")
            ->addSelect(['xs_status' => "if(`is_xs` = 1 && $op.`status` < 3, 1, 0)"])
            ->addSelect(['isrecommended' => 'if(`online_status`=1 && `isPrivate`=0, `recommendTime`, 0)'])
            ->addSelect(['effect_jixi_time' => 'if(`is_jixi`=1, `jixi_time`, 0)'])
            ->addSelect(['product_status' => "(case $op.`status` when 4 then 7 when 7 then 4 else $op.`status` end)"])
            ->joinWith('fangkuan')
            ->where(['del_status' => 0]);
        if ($request['name']) {
            $data->andFilterWhere(['like', 'title', $request['name']]);
        }
        if ($request['status'] == '0') {
            $data->andWhere(['online_status' => $request['status']]);
        } elseif ($request['status']) {
            $data->andWhere(['online_status' => OnlineProduct::STATUS_ONLINE, "$op.status" => $request['status']]);
        }
        //根据是否测试标进行过滤
        if (isset($request['isTest'])) {
            $isTest = $request['isTest'];
        } else {
            $isTest = Yii::$app->request->cookies->getValue('loanListFilterIsTest', 0);
        }
        $data->andWhere(['isTest' => $isTest]);
        if ($isTest) {
            Yii::$app->response->cookies->add(new Cookie(['name' => 'loanListFilterIsTest', 'value' => 1, 'expire' => strtotime('next year'), 'httpOnly' => false]));
        }

        $days = $request['days'];

        if (!empty($days)) {
            if (in_array($days, [1, 7])) {
                $data->andWhere(["$op.id" => $this->HkStats(intval($days))]);
            } else {
                throw $this->ex404();
            }
        }

        $_data = clone $data;
        $data->orderBy('xs_status desc, isrecommended desc, online_status asc, product_status asc,effect_jixi_time desc, sn desc');

        $pages = new Pagination(['totalCount' => $_data->count(), 'pageSize' => '20']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('list', [
            'models' => $model,
            'pages' => $pages,
            'status' => $status,
            'days' => $days,
            'isTest' => $isTest,
        ]);
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
     * 贷款的详细信息.
     */
    public function actionDetail()
    {
        //联表查出表前的记录。包括：已募集金额 **元 剩余可投金额：*元 已投资人数：**人 剩余时间：1天15小时6分
        $totalMoney = (new \Yii\db\Query())
                ->select('sum(order_money) as money')
                ->from('online_order')
                ->groupBy('online_pid')
                ->all();

        //联表查询出表格内容，联的是online_order 和user 共计两张表
        $query = (new \yii\db\Query())
                ->select('o.id,real_name,mobile,order_money,order_time,o.status')
                ->from(['online_order o'])
                ->innerJoin('user u', 'o.uid = u.id')
                ->all();

        return $this->render('detail', ['info' => $query, 'totalMoney' => $totalMoney]);
    }

    //搜索
    public function actionSearch()
    {
        $result = Yii::$app->request->get();
        var_dump($result);
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
                $updateData = ['status' => OnlineProduct::STATUS_FOUND, 'sort' => OnlineProduct::SORT_FOUND, 'full_time' => time(), 'finish_rate' => 1.0000];
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
                    $count = OnlineProduct::find()->where('recommendTime != 0')->andWhere(['isPrivate' => 0, 'del_status' => 0])->count();

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
            $model = OnlineProduct::findOne($id);
            if (empty($model) ||
              !in_array($model->status, [OnlineProduct::STATUS_NOW, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND, OnlineProduct::STATUS_HUAN]) ||
               empty($model->jixi_time)
             ) {
                return ['result' => '0', 'message' => '无法找到该项目,或者项目现阶段不允许开始计息'];
            }

            try {
                $this->initUserAssets($model);
            } catch (\Exception $e) {
                $message = $e->getMessage();

                Yii::trace('项目成立异常,项目ID为'.$model->id.
                    ',错误信息为'.$message.
                    ',操作时间为'.date('Y-m-d H:i:s').
                    ',操作人为'.$this->getAuthedUser()->username);

                return ['result' => '0', 'message' => $message];
            }

            $res = OnlineRepaymentPlan::generatePlan($model);
            if ($res) {
                //确认计息完成之后将标的添加至保全队列
                $job = new BaoQuanQueue(['itemId' => $id, 'status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_LOAN]);
                $job->save();

                return ['result' => '1', 'message' => '操作成功'];
            }

            return ['result' => '0', 'message' => '操作失败，请联系技术人员'];
        }

        return ['result' => '0', 'message' => 'ID不能为空'];
    }

    private function initUserAssets(OnlineProduct $model)
    {
        $orders = OnlineOrder::findAll(['online_pid' => $model->id, 'status'  => OnlineOrder::STATUS_SUCCESS]);

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
                'isTest' => $model->isTest,
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

        if ($deal->isPrivate) {
            return ['code' => 0, 'message' => '不允许推荐定向标'];
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
}
