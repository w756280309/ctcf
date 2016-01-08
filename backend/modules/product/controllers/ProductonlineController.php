<?php

namespace backend\modules\product\controllers;
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
use common\models\sms\SmsMessage;

/**
 * Description of OnlineProduct
 *
 * @author zhy-pc
 */
class ProductonlineController extends BaseController {

    public function init() {
        parent::init();
        if (Yii::$app->request->isAjax)
            Yii::$app->response->format = Response::FORMAT_JSON;
    }


    public function actionEdit($id = null) {
        $product_status = OnlineProduct::getProductStatusAll();
        $rongziUser = User::find()->where("type=2")->asArray()->all();
        $rongziInfo =[];
        foreach ($rongziUser as $k=>$v){
            $rongziInfo[$v['id']] = $v['org_name'];
        }
        $model = $id ? OnlineProduct::findOne($id) : new OnlineProduct();
        $ctmodel = array();
        $model->scenario = 'create';

        if ($id) {
            $model->start_date = date('Y-m-d H:i', $model->start_date);
            $model->end_date = date('Y-m-d  H:i', $model->end_date);
            $model->finish_date = date('Y-m-d  H:i', $model->finish_date);
            $model->jixi_time = $model->jixi_time?date('Y-m-d', $model->jixi_time):"";
            $model->yield_rate = bcmul($model->yield_rate, 100, 2);
            $model->fazhi = round($model->fazhi);
            $model->fazhi_up = round($model->fazhi_up);

            $ctmodel = ContractTemplate::find()->where(['pid'=>$id])->asArray()->all();
        }

        if ($model->load(Yii::$app->request->post())&&$model->validate()) {
            if(empty($id)){
                $model->sn = OnlineProduct::createSN();
                $model->sort = OnlineProduct::SORT_PRE;
            }
            $old = $model->oldAttributes;
            $new = $model->attributes;
            $diff = \Yii::$app->functions->timediff(strtotime(date('Y-m-d',  strtotime($model->start_date))),  strtotime(date('Y-m-d',strtotime($model->finish_date))));

            $start = strtotime($model->start_date);
            $end = strtotime($model->end_date);
            $finish = strtotime($model->finish_date);
            $jixi_time = !empty($model->jixi_time)?strtotime($model->jixi_time):"";
            $bool=true;
            if($start>$end||$start>$finish||$end>$finish){
                $bool=false;
            }

            $err="";
            if(!$model->is_jixi && !empty($model->jixi_time)) {
                if($jixi_time < $start) {
                    $err = '计息开始时间必须大于项目的募集开始时间';
                }else if($jixi_time > $finish) {
                    $err = '计息开始时间必须小于项目的截止时间';
                }

                if(!empty($id) && $model->online_status == OnlineProduct::STATUS_ONLINE) {
                    if($model->status == OnlineProduct::STATUS_FULL && $jixi_time < $model->full_time) {
                        $err = '计息开始时间必须大于项目满标时间';
                    }else if($model->status == OnlineProduct::STATUS_FOUND && $jixi_time < $model->full_time) {
                        $err = '计息开始时间必须大于项目提前募集结束时间';
                    }else if($jixi_time > $finish) {
                        $err = '计息开始时间必须小于项目的截止时间';
                    }
                }
            }

            $con_name_arr = Yii::$app->request->post('name');
            $con_content_arr = Yii::$app->request->post('content');

            if($model->expires>$diff['day']){
                $model->addError('expires',"项目天数 应该小于等于 项目截止日 - 募集开始时间;当前天数：".$diff['day'].'天');
            }else if(!$bool){
                $model->addError('start_date',"募集开始时间小于募集结束时间小于项目结束日");
                $model->addError('end_date',"募集开始时间小于募集结束时间小于项目结束日");
                $model->addError('finish_date',"募集开始时间小于募集结束时间小于项目结束日");
            }else if(!empty($err)) {
                $model->addError('jixi_time', $err);
            }else if(empty($con_name_arr) || empty($con_content_arr)) {
                $model->addError('contract_type','合同协议至少要输入一份');
            }else{
                $transaction = Yii::$app->db->beginTransaction();

                $model->start_date = strtotime($model->start_date);
                $model->end_date = strtotime($model->end_date);
                $model->finish_date = strtotime($model->finish_date);
                $model->creator_id = Yii::$app->user->id;
                $model->yield_rate = bcdiv($model->yield_rate, 100, 14);
                $model->jixi_time = strtotime($model->jixi_time);

                $pre = $model->save();
                if (!$pre) {
                    var_dump($model->getErrors());
                    $transaction->rollBack();
                    exit('录入ProductOnline异常');
                } else {
                    if ($id) {
                        ContractTemplate::deleteAll(['pid'=>$id]);
                    }

                    $record = new ContractTemplate();
                    foreach ($con_name_arr as $key => $val) {
                        $record_model = clone $record;
                        $record_model->pid = $id;
                        $record_model->name = $val;
                        $record_model->content = $con_content_arr[$key];
                        if(!$record_model->save()) {
                            $transaction->rollBack();
                            exit('录入ContractTemplate异常');
                        }
                    }
                }

                $transaction->commit();
                return $this->redirect(['list']);
            }
        }
        return $this->render('edit', ['pid' => $id, 'model' => $model,'ctmodel'=>$ctmodel, 'product_status' => $product_status,'rongziInfo'=>$rongziInfo]);
    }


    /**
     * 上线操作
     */
    public function actionLineon(){
        if(!Yii::$app->request->isPost){
            return [
                'result'=>0,
                'message'=>'非法请求'
            ];
        }
        $ids = Yii::$app->request->post('pids');
        OnlineProduct::updateAll(['online_status'=>1,'sort'=>OnlineProduct::SORT_PRE],"id in (".$ids.")");
        return ['result'=>1, 'message'=>'上线操作成功'];
    }

    public function actionProductinfo($sn = null) {
        $model = $sn ? OnlineProduct::findOne(["sn" => $sn]) : new OnlineProduct();
        if ($model == null) {
            $model = new OnlineProduct();
        }
        return json_encode(array(
            "title" => $model->title,
            "yield_rate" => $model->yield_rate,
            "product_duration" => $model->product_duration
        ));
    }

    /**
     * 删除
     * @param type $id
     * @param type $page
     * @return type
     */
    public function actionDelete() {
        if(!Yii::$app->request->isPost){
            return [
                'result'=>0,
                'message'=>'非法请求'
            ];
        }
        $id = Yii::$app->request->post('id');
        $model = $id ? OnlineProduct::findOne($id) : new OnlineProduct();
        if($model->status>1){
            return [
                'result'=>0,
                'message'=>'不允许删除'
            ];
        }
        $model->del_status = 1;
        $model->scenario = 'del';
        $model->save();
        return ['result'=>1, 'message'=>'成功'];
    }

    public function actionDelmore($ids = null) {
        $id_arr = explode(',', $ids);
        foreach ($id_arr as $id) {
            $_model = OnlineProduct::findOne($id);
            $_model->del_status = 1;
            $_model->scenario = 'del';
            $_model->save();
        }
        echo json_encode(array('res' => 1));
    }


    /**
     * 撤标
     * @param type $id
     */
    public function actionCancel($id = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (empty($id)) {
            return ['res' => 0, 'msg' => 'id为空'];
        }
        $online_product = OnlineProduct::findOne($id);
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
        $msg = $res ? "撤标成功" : "撤标失败";
        return ['res' => $res, 'msg' => $msg];
    }

    public function actionList()
    {
        $status = Yii::$app->params['deal_status'];
        $request = Yii::$app->request->get();
        $data = OnlineProduct::find()->where(array('del_status' => 0));
        if($request['name']) {
            $data->andFilterWhere(['like', 'title', $request['name']]);
        }
        if($request['status'] == '0') {
            $data->andWhere(['online_status' => $request['status']]);
        } else if ($request['status']) {
            $data->andWhere(['online_status' => OnlineProduct::STATUS_ONLINE,'status' => $request['status']]);
        }
        $data->orderBy('id desc');
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '20']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('list', [
                    'models' => $model,
                    'pages' => $pages,
                    'status' => $status
        ]);
    }
    //贷款的详细信息
    public function actionDetail($id=null){
//        联表查出表前的记录。包括：已募集金额 **元 剩余可投金额：*元 已投资人数：**人 剩余时间：1天15小时6分
        $totalMoney = (new \Yii\db\Query())
                ->select('sum(order_money) as money')
                ->from('online_order')
                ->groupBy('online_pid')
                ->all();

        //联表查询出表格内容，联的是online_order 和user 共计两张表
        $query = (new \yii\db\Query())
                ->select('o.id,real_name,mobile,order_money,order_time,o.status')
                ->from(['online_order o'])
                ->innerJoin('user u','o.uid = u.id')
                ->all();

        return $this->render('detail',['info'=>$query,'totalMoney'=>$totalMoney]);
    }
    //搜索
    public function actionSearch(){
        $result = Yii::$app->request->get();
        var_dump($result);
    }

    //ajax请求删除
    public function actionDel(){
        $id = Yii::$app->request->post("id");
        if($id){
            $model = OnlineProduct::findOne($id);
            $model->scenario = "del";
            $model->del_status = 1;
            if($model->save()){
                echo json_encode("success") ;
            }
        }
    }


    /**
     * 项目提前成立
     */
    public function actionFound($id=null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($id){
            $model = OnlineProduct::findOne($id);
            if(empty($model)||$model->status!=OnlineProduct::STATUS_NOW){
                return ['result' => '0', 'message' => '无法找到该项目,或者项目状态不是募集中'];
            }else{
                $bc = new BcRound();
                $transaction = Yii::$app->db->beginTransaction();
                $up_srs = OnlineProduct::updateAll(['status'=>OnlineProduct::STATUS_FOUND,'sort'=>OnlineProduct::SORT_FOUND],['id'=>$id]);
                if(!$up_srs){
                    $transaction->rollBack();
                    return ['result' => '0', 'message' => '操作失败,状态更新失败,请联系技术'];
                }
                $orders = OnlineOrder::getOrderListByCond(['online_pid'=>$id,'status'=>  OnlineOrder::STATUS_SUCCESS]);
                foreach ($orders as $ord) {
                    $ua = UserAccount::findOne(['type'=>  UserAccount::TYPE_LEND,'uid'=>$ord['uid']]);
                    $ua->investment_balance = $bc->bcround(bcadd($ua->investment_balance, $ord['order_money']),2);
                    $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $ord['order_money']),2);
                    if(!$ua->save()){
                        $transaction->rollBack();
                        return ['result' => '0', 'message' => '操作失败,账户更新失败,请联系技术'];
                    }
                }
                $transaction->commit();
                return ['result' => '1', 'message' => '操作成功'];
            }
        }else{
            return ['result' => '0', 'message' => 'ID不能为空'];
        }
    }

    /**
     * 确认起息
     * @param type $id
     * @return type
     */
    public function actionJixicorfirm(){
        $id = Yii::$app->request->post("id");
        Yii::$app->response->format = Response::FORMAT_JSON;
        if($id){
            $model = OnlineProduct::findOne($id);
            if(empty($model)||
              !in_array($model->status,[OnlineProduct::STATUS_NOW,OnlineProduct::STATUS_FULL,OnlineProduct::STATUS_FOUND,OnlineProduct::STATUS_HUAN])||
               empty($model->jixi_time)
             ){
                return ['result' => '0', 'message' => '无法找到该项目,或者项目现阶段不允许开始计息'];
            }else{
                $res = OnlineRepaymentPlan::createPlan($id);//转移到开始计息部分

                if($res){
                    return ['result' => '1', 'message' => '操作成功'];
                }else{
                    return ['result' => '0', 'message' => '操作失败，请联系技术'];
                }
            }
        }else{
            return ['result' => '0', 'message' => 'ID不能为空'];
        }
    }


    /**
     * 设置计息时间
     */
    public function actionJixi($product_id=null) {
        $this->layout=false;
        $c_flag = 0;
        $model = OnlineProduct::findOne($product_id);
        $model->scenario = 'jixi';
        if($model->is_jixi == 1) {
            $this->alert = 2;
            $this->msg = '已经开始计息，不允许修改计息开始时间';
        }else if($model->load(Yii::$app->request->post())) {
            $err = "";
            $model->jixi_time = strtotime($model->jixi_time);
            if($model->status == OnlineProduct::STATUS_FULL && $model->jixi_time < $model->full_time) {
                $err = '计息开始时间必须大于项目满标时间 '.date("Y-m-d",$model->full_time);
            }else if($model->status == OnlineProduct::STATUS_FOUND && $model->jixi_time < $model->full_time) {//?????
                $err = '计息开始时间必须大于项目提前募集结束时间 '.date("Y-m-d",$model->full_time);
            }else if($model->jixi_time > $model->finish_date) {
                $err = '计息开始时间必须小于项目的截止时间 '.date("Y-m-d",$model->finish_date);
            }

            if(!empty($err)) {
                $model->addError('jixi_time', $err);
            } else {
                $model->save();
                $this->alert = 1;
                $c_flag = 'close';
            }
        }

        if(!empty($model->jixi_time)) {
            $model->jixi_time = date('Y-m-d',$model->jixi_time);
        }

        return $this->render('jixi',['model' => $model, 'c_flag' => $c_flag]);
    }

    /**
     * 提前结束募集时间
     */
    public function actionEndProduct(){
        $res = 0;
        $id = Yii::$app->request->post("pid");
        Yii::$app->response->format = Response::FORMAT_JSON;

        if($id){
            $model = OnlineProduct::findOne($id);
            $model->scenario = "status";

            if($model->online_status == OnlineProduct::STATUS_ONLINE && $model->status == OnlineProduct::STATUS_NOW) {
                $model->status = OnlineProduct::STATUS_FOUND;
                $model->full_time = time();
                $res = $model->save();

                if ($res) {
                    $online_order = OnlineOrder::find()->where(['online_pid' => $model->id, 'status' => OnlineOrder::STATUS_SUCCESS])->groupBy('uid')->all();
                    $sms = new SmsMessage([
                        'template_id' => Yii::$app->params['sms']['manbiao']
                    ]);

                    foreach ($online_order as $order) {
                        $message = [
                            $order['username'],
                            $model->title
                        ];

                        $_sms = clone $sms;
                        $_sms->uid = $order['uid'];
                        $_sms->mobile = $order['mobile'];
                        $_sms->message = json_encode($message);

                        $_sms->save();
                    }
                }
            }
        }

        return ['res'=>$res,'msg'=>"",'data'=>''];
    }

}
