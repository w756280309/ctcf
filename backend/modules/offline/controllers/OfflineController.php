<?php

namespace backend\modules\offline\controllers;

use backend\controllers\BaseController;
use common\jobs\RepaymentJob;
use common\lib\product\ProductProcessor;
use common\models\adminuser\AdminLog;
use common\models\offline\OfflineOrder;
use common\models\affiliation\Affiliator;
use common\models\offline\OfflineLoan;
use common\models\offline\ImportForm;
use common\models\offline\OfflineRepayment;
use common\models\offline\OfflineRepaymentPlan;
use common\models\offline\OfflineStats;
use common\models\offline\OfflinePointManager;
use common\models\offline\OfflineUser;
use common\models\offline\OfflineUserManager;
use common\models\product\OnlineProduct;
use common\utils\ExcelUtils;
use common\utils\SecurityUtils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use common\models\mall\PointRecord;

class OfflineController extends BaseController
{
    /**
     * 录入线下数据页面
     *
     * @return string
     */
    public function actionAdd()
    {
        $model = new ImportForm();
        $flag = Yii::$app->request->post('flag');
        if (!empty($flag)) {
            $filename = $_FILES['ImportForm']['name']['excel'];
            if (!isset($filename) || empty($filename)) {
                $model->addError('excel', '未选择文件');
            }
            if (substr($filename, -4, 4) !== '.xls' && substr($filename, -5, 5) !== '.xlsx') {
                $model->addError('excel', '上传的文件为非.xlsx或.xls文件');
            }
            $filePath = $_FILES['ImportForm']['tmp_name']['excel'];
            $arr = [];
            try {
                $arr = ExcelUtils::readExcelToArray($filePath, 'M', 1002);
                if (count($arr) > 1001) {
                    $model->addError('excel', '数据必须小于1000行');
                }
            } catch (\Exception $ex) {
                $model->addError('excel', $ex->getMessage());
            }
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            if (!$model->hasErrors()) {
                try {
                    foreach ($arr as $key => $order) {
                        //省略第一行
                        if ($key < 1) {
                            continue;
                        }
                        //判断某一行皆为空时,跳过该行
                        if (empty(array_filter($order))) {
                            continue;
                        }
                        //初始化model，寻找行号，使用batchInsert插入
                        $neworder = $this->initModel($order);
                        if ($neworder->validate()) {
                            $neworder->save();
                            if ($neworder->valueDate) {
                                //更新积分和累计年化投资额
                                //$this->updatePointsAndAnnual($neworder, PointRecord::TYPE_OFFLINE_BUY_ORDER);
                            }
                        } else {
                            $error_index = $key + 1;
                            if ($neworder->hasErrors('loan_id')) {
                                throw new \Exception('Excel表中SN未找到对应标的,行号' . $error_index . ',请在后台添加标的: ' . $order[2]);
                            }
                            if ($neworder->hasErrors('affiliator_id')) {
                                throw new \Exception('文件内容有错,行号' . $error_index . ',请在后台添加分销商' . $order[0]);
                            }
                            if ($neworder->hasErrors('idCard')) {
                                throw new \Exception('身份证号不正确,行号' . $error_index . ',请更正身份证号' . $order[5]);
                            }
                            throw new \Exception('文件内容有错,行号' . $error_index);
                        }
                    }
                    $transaction->commit();
                    return $this->redirect('list');
                } catch (\Exception $ex) {
                    $model->addError('excel', $ex->getMessage());
                    $transaction->rollBack();
                }
                //删除临时文件
                @unlink($filepath);
            }
        }
        return $this->render('add', ['model' => $model]);
    }

    /**
     * 线下数据页面
     */
    public function actionList()
    {
        $request = Yii::$app->request->get();
        $ol = OfflineLoan::tableName();
        $o = OfflineOrder::tableName();
        $u = OfflineUser::tableName();
        $order = OfflineOrder::find()->innerJoinWith('loan')->innerJoinWith('user')->where(["$o.isDeleted" => false]);
        if (isset($request['bid']) && $request['bid'] > 0) {
            $order->andWhere(["$o.affiliator_id" => $request['bid']]);
        }
        if (isset($request['title']) && !empty($request['title'])) {
            $order->andFilterWhere(['like', "$ol.title", $request['title']]);
        }
        if (isset($request['realName']) && !empty($request['realName'])) {
            $order->andFilterWhere(['like', "$u.realName", $request['realName']]);
        }
        if (isset($request['mobile']) && !empty($request['mobile'])) {
            $order->andFilterWhere(['like', "$o.mobile", $request['mobile']]);
        }
        if (isset($request['loan_id']) && !empty($request['loan_id'])) {
            $order->andFilterWhere(["$o.loan_id" => $request['loan_id']]);
        }

        $branches = Affiliator::find()->all();
        $pages = new Pagination(['totalCount' => $order->count(), 'pageSize' => 10]);
        $totalmoney = $order->sum('money');
        $orders = $order->offset($pages->offset)->limit($pages->limit)->orderBy(["$o.id" => SORT_DESC])->all();
        $stats = OfflineStats::findOne(1);

        return $this->render('list', [
            'branches' => $branches,
            'orders' => $orders,
            'totalmoney' => $totalmoney,
            'pages' => $pages,
            'stats' => $stats,
        ]);
    }

    /**
     * 根据order数组，初始化一个新的OfflineOrder model
     *
     * @param $order
     * @return OfflineOrder
     */
    private function initModel($order)
    {
        //过滤掉所有的空格
        $order = array_map(function ($val) {
            return Yii::$app->functions->removeWhitespace($val);
        }, $order);
        $idCard = strtoupper($order[5]);
        $model = new OfflineOrder();
        $affiliator = Affiliator::find()->where(['name' => $order[0]])->one();
        $loan = OfflineLoan::find()->where(['sn' => $order[2]])->one();
        $user = OfflineUser::find()->where(['idCard' => $idCard])->one();
        $affiliator_id = null;
        $loan_id = null;
        if (null !== $affiliator) {
            $affiliator_id = $affiliator->id;
        }

        if (null !== $loan) {
            $loan_id = $loan->id;
        } else {
            $model->addError('loan_id', 'Excel表中SN未找到对应标的');
        }

        if (null !== $user) {
            $user_id = $user->id;
            //手机号应是始终为最后导入的那个
            if ($user->mobile !== $order[6]) {
                $user->mobile = $order[6];
                $user->save();
            }
        } else {
            $user = new OfflineUser();
            $user->realName = $order[4];
            $user->idCard = $idCard;
            $user->mobile = $order[6];
            $user->created_at = time();
            $user->save();
            $user_id = $user->id;
        }

        $model->affiliator_id = $affiliator_id;
        $model->loan_id = $loan_id;
        $model->user_id = $user_id;
        $model->idCard = $idCard;
        $model->mobile = $order[6];
        $model->accBankName = $order[7];
        $model->bankCardNo = $order[8];
        $model->money = $order[9];
        $model->orderDate = !empty($order[10])?(new \DateTime($order[10]))->format('Y-m-d') : null;
        $model->valueDate = !empty($order[11]) ? (new \DateTime($order[11]))->format('Y-m-d') : null;
        $model->apr = bcdiv(rtrim($order[12], '%'), 100, 4);
        $model->created_at = time();
        $model->isDeleted = false;
        return $model;
    }

    /**
     * 根据id删除对应的offline_order的一条记录（修改状态）
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');

        if ($id) {
            $order = OfflineOrder::findOne($id);
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $order->isDeleted = true;
                //修改标的修改记录
                $log = AdminLog::initNew($order);
                if ($order->save(false) && $log->save(false)) {
                    //如果存在计息日，才需要更新积分和累计年化投资额
                    if (false !== strtotime($order->valueDate)) {
                        //更新积分和累计年化投资额
                        $this->updatePointsAndAnnual($order, PointRecord::TYPE_OFFLINE_ORDER_DELETE);
                    }
                    $transaction->commit();
                    return ['code' => 1, 'message' => '删除成功'];
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                return ['code' => 0, 'message' => $ex->getMessage()];
            }
        }

        return ['code' => 0, 'message' => '删除失败'];
    }

    /**
     * 根据id/身份证号 编辑客户姓名/联系电话/开户行名称/银行卡号
     */
    public function actionEdit()
    {
        $request = Yii::$app->request->get();
        $o = OfflineOrder::tableName();
        $order = OfflineOrder::find()->innerJoinWith('loan')->where(["$o.isDeleted" => false]);
        if (isset($request['id'])) {
            $order->andWhere(["$o.id" => $request['id']]);
        }
        $model = $order->one();
        //$model->setScenario('edit');
        $model->realName = $model->user->realName;
        return $this->render('edit',[
            'model' => $model,
        ]);
    }

    /**
     * 更新线下数据客户信息
     */
    public function actionUpdate()
    {
        if (!Yii::$app->request->isPost) {
            return $this->redirect(['list']);
        }
        $post = Yii::$app->request->post();
        $transaction = Yii::$app->db->beginTransaction ();
        $order = $this->findOr404(OfflineOrder::class, $post['OfflineOrder']['id']);
        $offUser = $this->findOr404(OfflineUser::class,['idCard' => $order->idCard]);
        try {

            if ($order->load($post) && $order->validate()) {
                $res = $order->save();
                if (!$order->save()) {
                    throw new \Exception('用户信息更新失败!');
                }
            }
            //更新offlineUser表realName/mobile
            $offUser->realName =  $post['OfflineOrder']['realName'];
            if(isset($post['checkM']) && $post['checkM']){
                $offUser->mobile = $post['OfflineOrder']['mobile'];
            }
            if (!$offUser->save()) {
                throw new \Exception('用户真实姓名信息更新失败!');
            }
            $transaction->commit();
        } catch ( \Exception $e ) {
            $transaction->rollback ();
            Yii::$app->session->setFlash('info','修改失败！');
            return $this->redirect(['edit','id'=>$post['OfflineOrder']['id']]);
        }
        Yii::$app->session->setFlash('info','修改成功！');
        return $this->redirect(['list']);
    }

    /**
     * 编辑线下数据统计项.
     *
     * 1. 包括募集规模, 兑付本金, 兑付利息;
     * 2. 以上三项必须都有值且大于零;
     */
    public function actionEditStats()
    {
        $stats = $this->findOr404(OfflineStats::class, 1);

        if ($stats->load(Yii::$app->request->post()) && $stats->validate()) {
            if ($stats->tradedAmount <= 0) {
                $stats->addError('tradedAmount', '募集规模必须大于0');
            }

            if ($stats->refundedPrincipal <= 0) {
                $stats->addError('refundedPrincipal', '兑付本金必须大于0');
            }

            if ($stats->refundedInterest <= 0) {
                $stats->addError('refundedInterest', '兑付利息必须大于0');
            }

            $stats->updateTime = date('Y-m-d H:i:s');

            if (!$stats->hasErrors() && $stats->save(false)) {
                AdminLog::initNew($stats)->save(false);

                return $this->redirect('list');
            }
        }

        return $this->render('edit_stats', ['stats' => $stats]);
    }

    /**
     * 确认起息日
     */
    public function actionConfirm($id)
    {
        $this->layout = false;
        $refresh = false;
        if (empty($id) || null === ($model = OfflineOrder::findOne($id))) {
            throw $this->ex404();
        }
        if (!empty($model->valueDate)) {
            $model->addError('valueDate', '已有起息日期，不能再次确认');
        }
        if ($model->isDeleted) {
            $model->addError('valueDate', '该条线下订单已删除');
        }
        $model->scenario = 'confirm';
        if ($model->load(Yii::$app->request->post())) {
            if (empty($model->valueDate)) {
                $model->addError('valueDate', '起息日期不能为空');
            }
            if (empty($model->getErrors())) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $model->save();
                    $refresh = true;
                    $this->updatePointsAndAnnual($model, PointRecord::TYPE_OFFLINE_BUY_ORDER);
                    $transaction->commit();
                } catch (\Exception $ex) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('jixi', ['model' => $model, 'refresh' => $refresh]);
    }

    /**
     * 标的统一计息 offline_order表 已经计息的不再计息 value_date is null
     */

    public function actionLoanConfirm($id)
    {
        //正式的
        $loan = OfflineLoan::findOne($id);
        if (!is_null($loan) && $loan->is_jixi == false) {
            //将标的修改为确认计息状态
            $loan->is_jixi = true;
            //标的到期日
            $pp = new ProductProcessor();
            if ($loan->unit == '天') {
                $loan->finish_date = $pp->LoanTerms('d1',date('Y-m-d', strtotime($loan->jixi_time)), $loan->expires);
            } else if ($loan->unit == '个月') {
                $loan->finish_date = $pp->LoanTerms('m1',date('Y-m-d', strtotime($loan->jixi_time)), $loan->expires);
            }
            $transaction = Yii::$app->db->beginTransaction();
            if (!$loan->save(false)) {
                $transaction->rollBack();
            }
            //记录日志
            $log = AdminLog::initNew([
                'tableName' => OfflineLoan::tableName(),
                'primaryKey' => $loan->id
            ], Yii::$app->user, ['is_jixi' => true]);
            if (! $log->save(false)) {
                $transaction->rollBack();
            }
            $transaction->commit();
            //异步处理
            Yii::$app->queue->push(new RepaymentJob([
                'id' => $id,
                'operator' => Yii::$app->user->getId(),
                'action' => 'add',
            ]));
        }
        return $this->redirect('loanlist');
//        $this->layout = false;
//        $refresh = false;
//        $ofl = OfflineLoan::tableName();
//        $ofo = OfflineOrder::tableName();
//        if (empty($id)) {
//            throw $this->ex404();
//        }
//        $model = OfflineLoan::find()->innerJoinWith('order')
//            ->where(["$ofl.id" => $id , "$ofo.isDeleted" => false])
//            ->one();
//        if (null === $model) {
//            $model = OfflineLoan::find()->where(["$ofl.id" => $id])->one();
//        }
//        if (!empty($model->jixi_time)) {
//            $model->addError('jixi_time', '已有起息日期，不能再次确认');
//        }
//        //更新标的表 jixi_time
//        $model->scenario = 'confirm';
//        $post = Yii::$app->request->post();
//        if ($model->load($post)) {
//            if (empty($model->jixi_time)) {
//                $model->addError('jixi_time', '起息日期不能为空');
//            }
//            if (empty($model->getErrors())) {
//                $model->save();
//                $transaction = Yii::$app->db->beginTransaction();
//                if (null !== $model->order) {
//                    try {
//                        $refresh = true;
//                        foreach ($model->order as $order) {
//                            $order->scenario = 'confirm';
//                            if (empty($order->valueDate)) {
//                                $order->valueDate = $post['OfflineLoan']['jixi_time'];
//                                $order->save();
//                                $this->updatePointsAndAnnual($order, PointRecord::TYPE_OFFLINE_BUY_ORDER);
//                            }
//                        }
//                        $transaction->commit();
//                    } catch (\Exception $ex) {
//                        $transaction->rollBack();
//                    }
//                }
//            }
//        }
//
//        return $this->render('loanjixi', ['model' => $model, 'refresh' => $refresh]);
    }
    /**
     * 根据订单和类型更新积分和累计年化投资
     */
    private function updatePointsAndAnnual($order, $type)
    {
        //只有2017年的数据导入及删除才更改积分
        if (strtotime($order->orderDate) >= strtotime('2017-01-01')) {
            $pointManager = new OfflinePointManager();
            $pointManager->updatePoints($order, $type);
        }
        $offlineUserManager = new OfflineUserManager();
        $offlineUserManager->updateAnnualInvestment($order);
    }

    /**
     * 标的列表
     */
    public function actionLoanlist()
    {
        $model = new OfflineLoan();
        $request = Yii::$app->request->get();
        $query = OfflineLoan::find();
        //筛选标的sn
        if (!empty($request['sn'])) {
            $query->andWhere(['like', 'sn', trim($request['sn'])]);
        }
        if ($request['title']) {
            $query->andFilterWhere(['like', 'title', $request['title']]);
        }
        if ($request['id']) {
            $query->andFilterWhere(['id' => $request['id']]);
        }
        $query->orderBy(['id' => SORT_DESC, 'sn' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('loanlist' , [
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 新增标的
     */
    public function actionAddloan()
    {
        $model = new OfflineLoan();
        if ($id = Yii::$app->request->get('id')) {
            $model = $this->findOr404(OfflineLoan::className() , $id);
        }
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['OfflineLoan']['id']) {
                $model = OfflineLoan::findOne($post['OfflineLoan']['id']);
            }

            if ($model->load($post) && $model->save()) {
                return $this->redirect('loanlist');
            }
        }
        return $this->render('addloan' , [
            'model' => $model,
        ]);
    }

    /**
     * 编辑标的
     */
    public function actionEditloan()
    {
        $model = new OfflineLoan();

        if ($id = Yii::$app->request->get('id')) {
            $model = $this->findOr404(OfflineLoan::className() , $id);
        }
        $model->scenario = 'edit';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($post['OfflineLoan']['id']) {
                $model = OfflineLoan::findOne($post['OfflineLoan']['id']);
            }

            if ($model->load($post) && $model->save()) {
                return $this->redirect('loanlist');
            }
        }
        return $this->render('editloan' , [
            'model' => $model,
        ]);
    }

    /**
     * 标的分期列表
     */
    public function actionRepayment($id)
    {
        $request = Yii::$app->request->get();
        $query = OfflineRepayment::find()->innerJoinWith('loan')->where(['loan_id' => $request['id']])->orderBy('title asc,dueDate asc');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('repayment' , [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 增加分期
     */
    public function actionAddrpm()
    {
        $model = new OfflineRepayment();
        $loan_id = Yii::$app->request->get('loan_id');
        $id = Yii::$app->request->get('id');

        if ($id) {
            $model = OfflineRepayment::findOne($id);
            $loan_id = $model->loan_id;
            $offrpms = OfflineRepayment::find()->where(['loan_id' => $loan_id])->all();
            $dueDate = null;
            foreach ($offrpms as $offrpm) {
                if (is_null($dueDate)) {
                    $dueDate = $offrpm->dueDate;
                } else {
                    $dueDate .= ',' . $offrpm->dueDate;
                }
            }
            return $this->render('addrpm',[
                'model' => $model,
                'loan_id' => $loan_id,
                'dueDate' => $dueDate,
            ]);
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $loan_id = $post['OfflineRepayment']['loan_id'];
            $arr = explode(',',$post['OfflineRepayment']['dueDate']);

            array_multisort($arr,SORT_ASC);

            if ($post['OfflineRepayment']['term'] != sizeof($arr) && null !== $arr) {
                //日期个数与分期数量不符
                $model->addError('term', '日期个数与分期期数不符');
                return $this->render('addrpm',[
                    'model' => $model,
                    'loan_id' => $loan_id,
                ]);
            }
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (OfflineRepayment::find()->where(['loan_id' => $loan_id])->count() && null !== $arr) {
                    $model = OfflineRepayment::deleteAll(['loan_id' => $loan_id]);
                }
                foreach ($arr as $k => $v) {
                    $model = new OfflineRepayment();
                    if ($post['OfflineRepayment']['term'] == sizeof($arr)) {
                        $loan = OfflineLoan::findOne($loan_id);
                        $loan->finish_date = $v;
                        $loan->save(false);
                    }
                    $model->loan_id = $loan_id;
                    $model->term = $post['OfflineRepayment']['term'];
                    $model->dueDate = $v;
                    $model->save(false);
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
            }
            return $this->redirect(['repayment' , 'id'=>$loan_id]);
        }

        return $this->render('addrpm',[
            'model' => $model,
            'loan_id' => $loan_id,
        ]);
    }
    /**
     * 设置起息日
     * @params $type ['loan' => '标的', 'order' => '订单']
     * 目前只用作标的
     */
    public function actionJixi($id, $type) {
        $this->layout = false;
        $model = $type == 'loan' ? OfflineLoan::findOne($id) : OfflineOrder::findOne($id);
        $refresh = false;
        if ($model->load(Yii::$app->request->post())) {
            $res = $model->save(false);
            $refresh = true;
        }
        return $this->render('jixi_date', [
            'model' => $model,
            'type' => $type,
            'refresh' => $refresh,
            ]);
    }

    //还款计划
    public function actionRepaymentPlan($id)
    {
        $model = OfflineLoan::findOne(['id' => $id, 'is_jixi' => true]);
        return $this->render('repayment_plan', ['model' => $model]);
    }

    /** 重置还款计划
     * @param $id
     */
    public function actionResetRepayment($id)
    {
        $loan = OfflineLoan::findOne($id);
        //存在标的，并且存在还款计划
        if (!is_null($loan) && count($loan->repayments) > 0) {
            $transaction = Yii::$app->db->beginTransaction();
            //将标的的  is_jixi => false; finish_date => null;
            $loan->is_jixi = false;
            $loan->finish_date = null;
            if (!$loan->save()) {
                $transaction->rollBack();
            } else {
                //记录日志
                $log = AdminLog::initNew([
                    'tableName' => OfflineLoan::tableName(),
                    'primaryKey' => $loan->id
                ], Yii::$app->user, ['is_jixi' => false, 'finish_date' => null]);
                if (! $log->save(false)) {
                    $transaction->rollBack();
                } else {
                    $transaction->commit();
                    //异步删除offline_repayment & offline_repayment_plan
                    Yii::$app->queue->push(new RepaymentJob([
                        'id' => $id,
                        'operator' => Yii::$app->user->getId(),
                        'action' => 'del',
                    ]));
                }
            }
        }
        return $this->redirect('loanlist');
    }

    //付息
    public function actionFuxi($id, $type)
    {
        $this->layout = false;
        $model = $type == 'plan' ? OfflineRepaymentPlan::findOne($id) : OfflineRepayment::findOne($id);
        $refresh = false;
        if (Yii::$app->request->post()) {
            $plan_id = [];
            if ($type == 'plan') {  //单个还款计划
                $model->actualRefundTime = Yii::$app->request->post('fuxi_date');
                $model->status = 1; //已还
                $model->operator = Yii::$app->user->getId();
                if ($model->save(false)) {
                    $plan_id[] = $model->id;
                    $refresh = true;
                }
            } else if ($type == 'repayment') {  //每期还款计划
                $plans = OfflineRepaymentPlan::find()->where([
                    'loan_id' => $model->loan->id,
                    'qishu' => $model->term,
                    'status' => 0,
                ])->all();
                if (!empty($plans)) {
                    foreach ($plans as $plan) {
                        $plan->actualRefundTime = Yii::$app->request->post('fuxi_date');
                        $plan->status = 1; //已还
                        $plan->operator = Yii::$app->user->getId();
                        if ($plan->save(false)) {
                            $plan_id[] = $plan->id;
                        }
                    }
                    $refresh = true;
                }
            }
            //异步发短信
            Yii::$app->queue->push(new RepaymentJob([
                'id' => $plan_id,
                'operator' => Yii::$app->user->getId(),
                'action' => 'sendsms',
            ]));
        }
        return $this->render('fuxi', [
            'type' => $type,
            'refresh' => $refresh,
        ]);
    }

    //单个执行还款，发短信
    public static function fuxiSingle(OfflineRepaymentPlan $plan, $date)
    {
        $plan->actualRefundTime = $date;
        $plan->status = 1; //已还
        $plan->operator = Yii::$app->user->getId();
        if ($plan->save(false)) {
            //发短信

        } else {
            throw new \Exception('还款计划【'.$plan->id.'】编辑失败');
        }
    }
}
