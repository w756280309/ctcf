<?php

namespace backend\modules\product\controllers;

use common\models\order\BaoQuanQueue;
use Yii;
use yii\web\Response;
use common\models\product\OnlineProduct;
use yii\data\Pagination;
use backend\controllers\BaseController;
use common\models\contract\ContractTemplate;
use common\models\user\User;
use common\models\order\OnlineRepaymentPlan;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\booking\BookingLog;
use P2pl\Borrower;
use common\service\LoanService;
use common\lib\product\ProductProcessor;
use common\models\user\MoneyRecord;
use common\utils\TxUtils;
use yii\web\NotFoundHttpException;

/**
 * Description of OnlineProduct.
 *
 * @author zhy-pc
 */
class ProductonlineController extends BaseController
{
    public function init()
    {
        parent::init();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }
    }

    /**
     * 新增、编辑标的项目.
     */
    public function actionEdit($id = null)
    {
        $rongziUser = User::find()->where(['type' => User::USER_TYPE_ORG])->asArray()->all();
        $rongziInfo = [];
        foreach ($rongziUser as $v) {
            $rongziInfo[$v['id']] = $v['org_name'];
        }
        $model = $id ? OnlineProduct::findOne($id) : new OnlineProduct();
        $ctmodel = null;
        $model->scenario = 'create';
        if (!empty($id)) {
            $model->is_fdate = (0 === $model->finish_date) ? 0 : 1;
            $model->yield_rate = bcmul($model->yield_rate, 100, 2);
            $isPrivate = $model->isPrivate;
            $ctmodel = ContractTemplate::find()->where(['pid' => $id])->all();
        } else {
            $model->epayLoanAccountId = '';
            $model->fee = 0;
            $model->funded_money = 0;
            $model->full_time = 0;
            $model->yuqi_faxi = 0;
        }

        $con_name_arr = Yii::$app->request->post('name');
        $con_content_arr = Yii::$app->request->post('content');
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $uids = LoanService::convertUid($model->allowedUids);
            if (!empty($model->finish_date) && OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int)$model->refund_method) {
                //若截止日期不为空，重新计算项目天数
                $pp = new ProductProcessor();
                $model->expires = $pp->LoanTimes($model->start_date, null, strtotime($model->finish_date), 'd', true)['days'][1]['period']['days'];
            }
            if (null === $model->id) {
                $model->sn = OnlineProduct::createSN();
                $model->sort = OnlineProduct::SORT_PRE;
            } else {
                $model->isPrivate = $isPrivate;
            }

            $_namearr = empty($con_name_arr) ? $con_name_arr : array_filter($con_name_arr);
            if (empty($_namearr)) {
                $model->addError('contract_type', '合同协议至少要输入一份');
            }

            if (false === strpos($con_name_arr[0], '认购协议')) {
                $model->addError('contract_type', '合同名称错误,第一份合同应该录入认购协议');
            }
            if (false === strpos($con_name_arr[1], '风险揭示书')) {
                $model->addError('contract_type', '合同名称错误,第二份合同应该录入风险揭示书');
            }

            foreach($con_name_arr as $key => $val) {
                if (empty($val)) {
                    $model->addError('contract_type', '合同名称不能为空');
                }
                if (empty($con_content_arr[$key])) {
                    $model->addError('contract_type', '合同内容不能为空');
                }
            }

            if (!$model->getErrors('contract_type')) {
                $transaction = Yii::$app->db->beginTransaction();
                $model->allowedUids = $uids;
                $model->start_date = strtotime($model->start_date);
                $model->end_date = strtotime($model->end_date);
                $model->finish_date = $model->finish_date !== null ? strtotime($model->finish_date) : 0;
                $model->creator_id = Yii::$app->user->id;
                $model->yield_rate = bcdiv($model->yield_rate, 100, 14);
                $model->jixi_time = $model->jixi_time !== '' ? strtotime($model->jixi_time) : 0;
                $model->recommendTime = empty($model->recommendTime) ? 0 : $model->recommendTime;
                $pre = $model->save(false);
                if (!$pre) {
                    $transaction->rollBack();
                    $model->addError('title', '标的添加异常');
                }
                if (!empty($id)) {
                    ContractTemplate::deleteAll(['pid' => $id]);
                }
                $record = new ContractTemplate();
                foreach ($con_name_arr as $key => $val) {
                    $record_model = clone $record;
                    $record_model->pid = $model->id;
                    $record_model->name = $val;
                    $record_model->content = $con_content_arr[$key];
                    if (!$record_model->save()) {
                        $transaction->rollBack();
                        $model->addError('title', '录入ContractTemplate异常');
                    }
                }
                if (!$model->hasErrors()) {
                    $transaction->commit();

                    return $this->redirect(['list']);
                }
            }
        }

        return $this->render('edit', [
            'pid' => $id,
            'model' => $model,
            'ctmodel' => $ctmodel,
            'rongziInfo' => $rongziInfo,
            'con_name_arr' => $con_name_arr,
            'con_content_arr' => $con_content_arr,
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
                OnlineProduct::updateAll(['epayLoanAccountId' => $resp, 'online_status' => 1], 'id='.$loan['id']);
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
     * 删除.
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
            throw new NotFoundHttpException();     //参数无效,抛出404异常
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
    public function actionCancel($id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
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
        $order = new \common\models\order\OnlineOrder();
        $res = $order->cancelOnlinePro($id);
        $msg = $res ? '撤标成功' : '撤标失败';

        return ['res' => $res, 'msg' => $msg];
    }

    /**
     * 贷款管理->项目列表页
     */
    public function actionList()
    {
        $status = Yii::$app->params['deal_status'];
        $request = Yii::$app->request->get();

        $op = OnlineProduct::tableName();
        $of = \common\models\order\OnlineFangkuan::tableName();
        $data = (new \yii\db\Query())
            ->select("$op.*, $of.status as fstatus")
            ->from($op)
            ->leftJoin($of, "$op.id = $of.online_product_id")
            ->where(array("$op.del_status" => 0));
        if ($request['name']) {
            $data->andFilterWhere(['like', 'title', $request['name']]);
        }
        if ($request['status'] == '0') {
            $data->andWhere(['online_status' => $request['status']]);
        } elseif ($request['status']) {
            $data->andWhere(['online_status' => OnlineProduct::STATUS_ONLINE, "$op.status" => $request['status']]);
        }
        $data->orderBy("recommendTime desc, $op.id desc");
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '20']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('list', [
                    'models' => $model,
                    'pages' => $pages,
                    'status' => $status,
        ]);
    }

    /**
     * 贷款的详细信息
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

    //ajax请求删除
    public function actionDel()
    {
        $id = Yii::$app->request->post('id');
        if ($id) {
            $model = OnlineProduct::findOne($id);
            $model->scenario = 'del';
            $model->del_status = 1;
            if ($model->save()) {
                echo json_encode('success');
            }
        }
    }

    /**
     * 项目提前成立.
     */
    public function actionFound()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');
        if ($id) {
            $model = OnlineProduct::findOne($id);
            if (empty($model) || $model->status != OnlineProduct::STATUS_NOW) {
                return ['result' => '0', 'message' => '无法找到该项目,或者项目状态不是募集中'];
            } else {
                $bc = new BcRound();
                $transaction = Yii::$app->db->beginTransaction();
                $up_srs = OnlineProduct::updateAll(['status' => OnlineProduct::STATUS_FOUND, 'sort' => OnlineProduct::SORT_FOUND, 'full_time' => time()], ['id' => $id]);
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
                    $mrmodel->remark = '项目成立,冻结金额转入理财金额账户。交易金额'. $ord['order_money'];
                    if (!$ua->save() || !$mrmodel->save()) {
                        $transaction->rollBack();
                        return ['result' => '0', 'message' => '操作失败,账户更新失败,请联系技术'];
                    }
                }

                if (!empty($model->recommendTime)) {
                    $count = OnlineProduct::find()->where("recommendTime != 0")->andWhere(['isPrivate' => 0])->count();

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
     * 确认起息.
     *
     * @param type $id
     *
     * @return type
     */
    public function actionJixicorfirm()
    {
        $id = Yii::$app->request->post('id');
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($id) {
            $model = OnlineProduct::findOne($id);
            if (empty($model) ||
              !in_array($model->status, [OnlineProduct::STATUS_NOW, OnlineProduct::STATUS_FULL, OnlineProduct::STATUS_FOUND, OnlineProduct::STATUS_HUAN]) ||
               empty($model->jixi_time)
             ) {
                return ['result' => '0', 'message' => '无法找到该项目,或者项目现阶段不允许开始计息'];
            } else {
                //$res = OnlineRepaymentPlan::createPlan($id);//转移到开始计息部分old
                $res = OnlineRepaymentPlan::generatePlan($model);
                if ($res) {
                    //确认计息完成之后将标的添加至保全队列
                    $job = new BaoQuanQueue(['proId' => $id, 'status' => BaoQuanQueue::STATUS_SUSPEND]);
                    $job->save();
                    return ['result' => '1', 'message' => '操作成功'];
                } else {
                    return ['result' => '0', 'message' => '操作失败，请联系技术'];
                }
            }
        } else {
            return ['result' => '0', 'message' => 'ID不能为空'];
        }
    }

    /**
     * 设置计息时间.
     */
    public function actionJixi($product_id)
    {
        if (empty($product_id)) {
            throw new NotFoundHttpException();   //当参数无效时,抛出404异常
        }

        $c_flag = 0;
        $model = OnlineProduct::findOne($product_id);
        if (null === $model) {
            throw new NotFoundHttpException();
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
     * 标的推荐功能
     */
    public function actionRecommend()
    {
        $dealId = Yii::$app->request->get('id');

        $deal = OnlineProduct::findOne($dealId);
        if (!$deal) {
            throw new NotFoundHttpException('The deal info is not existed.');
        }

        if ($deal->isPrivate) {
            return ['code' => 0, 'message' => '不允许推荐定向标'];
        }

        if (!empty($deal->recommendTime)) {
            $count = OnlineProduct::find()->where("recommendTime != 0")->andWhere(['isPrivate' => 0])->count();

            if ($count <= 1) {
                return ['code' => 0, 'message' => '推荐标的至少要有一个'];
            }

            $deal->recommendTime = 0;
        } else {
            $deal->recommendTime = time();
        }

        if (!$deal->save(false)) {
            return ['code' => 0, 'message' => '操作失败'];
        }

        return ['code' => 1, 'message' => '操作成功'];
    }
}
