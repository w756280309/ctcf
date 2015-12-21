<?php

namespace backend\modules\order\controllers;

use Yii;
use common\models\order\OfflineOrder;
use common\models\user\User;
use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use common\models\product\OfflineProduct;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use backend\controllers\BaseController;
use yii\web\Response;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineFangkuanDetail;
use common\models\order\OnlineRepaymentPlan;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class OrderController extends BaseController
{
    public $layout = 'main';
    
    /**
     * Lists all OfflineOrder models.
     * @return mixed
     */
    public function actionIndex($user_id=null)
    {
        $user = User::findOne($user_id);
        $status_arr = OfflineOrder::getStatusList();
        $toubiao_arr = OfflineOrder::getToubiaoTypeList();
        //var_dump(Yii::$app->user->id,Yii::$app->user->getIdentity()->username);
        $view_search = Yii::$app->request->get();
        
        foreach ($view_search as $key=>$val){
            if(empty($val)){
                unset($view_search[$key]);
            }
        }
        if(!empty($user)){
            $view_search['user_id'] = $user['id'];
        }
        $copy_search = $view_search;
        $order_time="";
        $pay_time="";
        $product_title="";
        if(isset($view_search['order_time'])&&$view_search['order_time']){
            $order_time=$view_search['order_time'];
            unset($view_search['order_time']);
        }
        if(isset($view_search['pay_time'])&&$view_search['pay_time']){
            $pay_time=$view_search['pay_time'];
            unset($view_search['pay_time']);
        }
        if(isset($view_search['product_title'])&&$view_search['product_title']){
            $product_title=$view_search['product_title'];
            unset($view_search['product_title']);
        }
        unset($view_search['uid']);
        unset($view_search['r']);
        $search = $view_search;
        $order = OfflineOrder::find()->andWhere($search);//->andFilterWhere([''])
        if(!empty($order_time)){
            $order->andFilterWhere(['<','order_time',  strtotime($order_time." 23:59:59")]);
            $order->andFilterWhere(['>','order_time',  strtotime($order_time." 0:00:00")]);
        }
        if(!empty($pay_time)){
            $order->andFilterWhere(['<','pay_time',  strtotime($pay_time." 23:59:59")]);
            $order->andFilterWhere(['>','pay_time',  strtotime($pay_time." 0:00:00")]);
        }
        if(!empty($product_title)){
            $order->andFilterWhere(['like','product_title',  $product_title]);
        }
        
        $order->andFilterWhere(['<','status', OfflineOrder::STATUS_DEL]);
        
        $count = $order->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => '10']);
        $model = $order->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();     
        
        return $this->render('index',['status_arr'=>$status_arr,'toubiao_arr'=>$toubiao_arr,'model'=>$model,'pages' => $pages,'user'=>$user,'count'=>$count,'search'=>$copy_search]);
    }
    
    public function actionEdit($id = null,$uid = null){
        
        $user = User::findOne($uid);
        if(empty($uid)){
            throw new NotFoundHttpException('用户不能为空.');
        }
        $model = $id ? OfflineOrder::findOne($id) : new OfflineOrder();
        $status_arr = OfflineOrder::getStatusList();
        $toubiao_arr = OfflineOrder::getToubiaoTypeList();
        if($id){
            $model->scenario = 'update';
        }else{
            $model->scenario = 'create';
            $model->creator_id = Yii::$app->user->id;
        }
        
        
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->product_sn = $_POST['OfflineOrder']['product_sn'];
            if(!OfflineProduct::findOne(['sn'=>  $model->product_sn])){
                $model->addError('sn', '产品错误。');
            }
            
            $model->order_time = strtotime($model->order_time);
            $model->pay_time = strtotime($model->pay_time); 
            
            return json_encode($model->save());
//            $model->save();
//            return $this->redirect(['/order/order?uid='.$uid]);
        }
        $model->order_time = date("Y-m-d H:i:s",(empty($model->order_time)?time():$model->order_time));
        $model->pay_time = date("Y-m-d H:i:s",(empty($model->pay_time)?time():$model->pay_time)); 
        return $this->render('edit',['model'=>$model,"status_arr"=>$status_arr,'toubiao_arr'=>$toubiao_arr,'user'=>$user]);
    }

    /**
     * Deletes an existing OfflineOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = OfflineOrder::STATUS_DEL;
        $model->scenario = 'update';
        $model->save();
        return $this->redirect(['index']);
    }

    public function actionView($id = null){
        $model = $this->findModel($id);
        return $this->render('view',['model'=>$model]);
    }
    /**
     * Finds the OfflineOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return OfflineOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OfflineOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionSpecialorder($psn=null){
        $view_search = Yii::$app->request->get();
//        unset($view_search['psn']);
//        foreach ($view_search as $key=>$val){
//            if(empty($val)){
//                unset($view_search[$key]);
//            }
//        }
        $order = OfflineOrder::find()->where(['type'=>1]);
        if(isset($view_search['order_time'])&&$view_search['order_time']){
            $order->andFilterWhere(['<','order_time',  strtotime($view_search['order_time']." 23:59:59")]);
            $order->andFilterWhere(['>','order_time',  strtotime($view_search['order_time']." 0:00:00")]);
        }
        if(isset($view_search['username'])&&$view_search['username']){
            $order->andFilterWhere(['like','username',  $view_search['username']]);
        }
        if(isset($view_search['psn'])&&$view_search['psn']){
             $order->andWhere(['product_sn'=>$view_search['psn']]);
        }
        if(isset($view_search['order_money'])&&$view_search['order_money']){
            $order->andWhere(['order_money'=>$view_search['order_money']]);
        }
        if(isset($view_search['deposit_status'])&&$view_search['deposit_status']){
            $order->andWhere(['deposit_status'=> $view_search['deposit_status']-1]);
        }
       if(isset($view_search['deal_status'])&&$view_search['deal_status']){
            $order->andWhere(['deal_status'=> $view_search['deal_status']-1]);
        }  
        $count = $order->count();
        $pages = new Pagination(['totalCount' =>$count, 'pageSize' => '10']);
        $model = $order->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();     
        
        return $this->render('specialorder',['model'=>$model,'pages' => $pages,'count'=>$count,'search'=>$view_search,'psn'=>$psn]);
    }
    
    /**
     * 返还特殊资产保证金
     */
    public function actionReturnbao($id=null,$uid=null){
        $_model = OfflineOrder::findOne($id);
        if(!$_model->deposit_status){
            echo json_encode(array('res' => 0,'msg'=>"没有缴纳保证金"));exit;
        }
        if($_model->deposit_return_status){
            echo json_encode(array('res' => 0,'msg'=>"已经返还保证金"));exit;
        }
        $_model->deposit_money = 0;
        $_model->deposit_return_status = 1;
        $_model->scenario = 'deposit_return';
        $_model->save();
        echo json_encode(array('res' => 1));exit;
    }
    
    /**
     * 特殊资产编辑是否成交是否缴纳保证金
     */
    public function actionSpecialview($id=null,$psn=null){
        $model = OfflineOrder::findOne($id);
        $model->scenario = 'special_edit';
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
           $model->save();
        }
        return $this->render('specialview',['model'=>$model,'psn'=>$psn]);
    }
    
    
    /**
     * 放款审核验证
     */
    public function actionCheckfk(){
        return $this->fkExamin(OnlineFangkuan::STATUS_EXAMINED);        
    }
    
    /**
     * 放款审核拒绝
     */
    public function actionFkdeny(){
        //return $this->fkExamin(OnlineFangkuan::STATUS_DENY); 
        $status = OnlineFangkuan::STATUS_DENY;
        Yii::$app->response->format = Response::FORMAT_JSON;
        $oids = Yii::$app->request->post('oids');
        $pid = Yii::$app->request->post('pid');
        $remark = Yii::$app->request->post('remark');
        $id_arr = explode('_', $oids);
        $ofkdc = OnlineFangkuanDetail::find()->where(['product_order_id'=>$id_arr,'online_product_id'=>  $pid])->count();
        if($ofkdc){
            return ['res'=>0,'msg'=>'包含已经做过批次审核操作的订单']; 
        }
        $orders = OnlineOrder::find()->where(['id'=>$id_arr,'status'=>  OnlineOrder::STATUS_SUCCESS])->asArray()->select('id,order_money,refund_method,yield_rate,expires,uid,order_time')->all();
        if(!count($orders)){
            return ['res'=>0,'msg'=>'数据异常']; 
        }
        $product = OnlineProduct::find()->where(['id'=>$pid])->one();
        $total = 0;
        foreach($orders as $val){
            $total=bcadd($total,$val['order_money']);
        }
        bcscale(14);
        $bcround = new BcRound();
        /*生成放款批次 start*/
        $transaction  = Yii::$app->db->beginTransaction();
        $fkm = new OnlineFangkuan();
        
        $ofkd = new OnlineFangkuanDetail();
        $fkdstatus = $status;
        foreach ($orders as $order){
            $ofk = clone $fkm;
            $ofk->sn = OnlineFangkuan::createSN();
            $ofk->order_money = $bcround->bcround($total, 2);
            $ofk->online_product_id = $pid;
            $ofk->fee = 0;
            $ofk->uid = $product->borrow_uid;//借款人uid
            $ofk->status = $status;
            $ofk->remark = $remark;
            $ofk->admin_id = Yii::$app->user->id;
            $fkre = $ofk->save();
            if(!$fkre){
                $transaction->rollBack(); 
                return ['res'=>0,'msg'=>'放款批次异常']; 
            }
            
            $ofkd_model = clone $ofkd;
            $ofkd_model->fangkuan_order_id=$ofk->id;
            $ofkd_model->product_order_id=$order['id'];
            $ofkd_model->order_money=$order['order_money'];
            $ofkd_model->online_product_id=$pid;
            $ofkd_model->order_time=$order['order_time'];
            $ofkd_model->admin_id=Yii::$app->user->id;
            $ofkd_model->status=$fkdstatus;
            if(!$ofkd_model->save()){
                $transaction->rollBack(); 
                return ['res'=>0,'msg'=>'放款批次详情异常']; 
            }
        }
        $transaction->commit();
         return ['res'=>1,'data'=>$total,'msg'=>'操作成功']; 
        /*生成放款批次 end */
    }
    
    public function fkExamin($status=2){
        Yii::$app->response->format = Response::FORMAT_JSON;
        bcscale(14);
        $bcround = new BcRound();
        $oids = Yii::$app->request->post('oids');
        $pid = Yii::$app->request->post('pid');
        $remark = Yii::$app->request->post('remark');
        $id_arr = explode('_', $oids);
        $orders = OnlineOrder::find()->where(['id'=>$id_arr,'status'=>  OnlineOrder::STATUS_SUCCESS])->asArray()->select('id,order_money,refund_method,yield_rate,expires,uid,order_time')->all();
        if(count($orders)!=count($id_arr)){
            return ['res'=>0,'msg'=>'项目当前状态异常']; 
        }
        $product = OnlineProduct::find()->where(['id'=>$pid])->one();
        if($product->status!=OnlineProduct::STATUS_FULL&&$product->status!=OnlineProduct::STATUS_FOUND){
            return ['res'=>0,'msg'=>'项目不可放款']; 
        }
        if(empty($product)){
            return ['res'=>0,'msg'=>'项目无法获取']; 
        }
        $ofkdc = OnlineFangkuanDetail::find()->where(['product_order_id'=>$id_arr,'status'=>  OnlineFangkuan::STATUS_EXAMINED])->count();
        if($ofkdc){
            return ['res'=>0,'msg'=>'包含已生成放款记录的订单']; 
        }
               
        $total = 0;
        foreach($orders as $val){
            $total=bcadd($total,$val['order_money']);
        }
        /*如果设立阀值，需要的另外一些验证*/
        if(bcdiv($product->fazhi,1)&&$status==OnlineFangkuan::STATUS_EXAMINED){
            //先判断有没有放过
            $fkcount = OnlineFangkuan::find()->where(['online_product_id'=>$pid])->count();
            if($fkcount){
                $total_money = OnlineFangkuan::find()->where(['online_product_id'=>$pid,'status'=>[OnlineFangkuan::STATUS_EXAMINED,OnlineFangkuan::STATUS_FANGKUAN]])->sum("order_money");
                //如果放过，需要判断是否超过阀值递增额度
                $bool = $bcround->bcround(bcdiv(bcsub($product->money,bcadd($total_money,$total)),1),2)*1;
                //var_dump($bool);exit;
                if($bool){
                    if(bcsub($total,$product->fazhi_up)<0){
                        return ['res'=>0,'data'=>'','msg'=>'不足阀值递增金额']; 
                    }
                }
            }else{
                //如果没有放过最低要满足阀值的要求
                if(bcsub($total,$product->fazhi)<0){
                    return ['res'=>0,'data'=>'','msg'=>'不足阀值']; 
                }
            }
        }else{//不设阀值的时候需要全部放
            $read_count = OnlineOrder::find()->where(['online_pid'=>$pid,'status'=>  OnlineOrder::STATUS_SUCCESS])->count('id');
            if(count($orders)!=$read_count){
                return ['res'=>0,'data'=>'','msg'=>'此标不设阀值需要一次性全部审核']; 
            }
        }
        /*生成放款批次 start*/
        $transaction  = Yii::$app->db->beginTransaction();
        $ofk = new OnlineFangkuan();
        $ofk->sn = OnlineFangkuan::createSN();
        $ofk->order_money = $bcround->bcround($total, 2);
        $ofk->online_product_id = $pid;
        $ofk->fee = 0;
        $ofk->uid = $product->borrow_uid;//借款人uid
        $ofk->status = $status;
        $ofk->remark = $remark;
        $ofk->admin_id = Yii::$app->user->id;
        $fkre = $ofk->save();
        if(!$fkre){
            $transaction->rollBack(); 
            return ['res'=>0,'msg'=>'放款批次异常']; 
        }
        $ofkd = new OnlineFangkuanDetail();
        $fkdstatus = $status;
        foreach ($orders as $order){
            $ofkd_model = clone $ofkd;
            $ofkd_model->fangkuan_order_id=$ofk->id;
            $ofkd_model->product_order_id=$order['id'];
            $ofkd_model->order_money=$order['order_money'];
            $ofkd_model->online_product_id=$pid;
            $ofkd_model->order_time=$order['order_time'];
            $ofkd_model->admin_id=Yii::$app->user->id;
            $ofkd_model->status=$fkdstatus;
            if(!$ofkd_model->save()){
                $transaction->rollBack(); 
                return ['res'=>0,'msg'=>'放款批次详情异常']; 
            }
        }
        if($status==OnlineFangkuan::STATUS_EXAMINED){
            $planres = OnlineRepaymentPlan::createPlan($pid,$orders);
            if(!$planres){
                $transaction->rollBack(); 
                return ['res'=>0,'msg'=>'还款计划异常']; 
            }
        }
        $transaction->commit();
        /*生成放款批次 end */
        return ['res'=>1,'data'=>$total,'msg'=>'批次'.($ofk->sn).'操作成功']; 

    }
    
    
    public function actionFkop(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        bcscale(14);
        $bcround = new BcRound();
        $oids = Yii::$app->request->post('oids');
        $pid = Yii::$app->request->post('pid');
        $fksn = Yii::$app->request->post('fksn');
        $fksn_arr = explode('_', $fksn);
        if(count($fksn_arr)==0){
            return ['res'=>0,'msg'=>'请选择至少一个批次']; 
        }
        $fksn_arr = array_unique($fksn_arr);
        $product = OnlineProduct::findOne($pid);
        $boolstatus = FALSE;
        if($product->status==OnlineProduct::STATUS_FULL||$product->status==OnlineProduct::STATUS_FOUND){
            $boolstatus=TRUE;
        }
        if(!$boolstatus){
            return ['res'=>0,'msg'=>'标的状态异常，当前状态码：'.$product->status]; 
        }
        $oid_arr = explode('_', $oids);
        $fkarr = array();
        $optotal = 0;//记录操作的总金额
        
        foreach ($fksn_arr as $key=>$val){
            $fk = OnlineFangkuan::findOne($val);
            if(empty($fk)){
                return ['res'=>0,'msg'=>'包含未存在批次']; 
            }else if($fk->status!=OnlineFangkuan::STATUS_EXAMINED){
                return ['res'=>0,'msg'=>'已放款，不能重复放款']; 
            }
            
            $real_fkd_count = OnlineFangkuanDetail::find()->where(['fangkuan_order_id'=>$val,'status'=>OnlineFangkuan::STATUS_EXAMINED])->count('id');
            $read_fkd_count = OnlineFangkuanDetail::find()->where(['fangkuan_order_id'=>$val,'product_order_id'=>$oid_arr,'status'=>OnlineFangkuan::STATUS_EXAMINED])->count('id');
            if($real_fkd_count!=$read_fkd_count){
                return ['res'=>0,'msg'=>'同一批次必须同时放款']; 
            }
            $fkarr[$key] = $fk;
            $optotal=bcadd($optotal,$fk->order_money);
        }
        $transaction  = Yii::$app->db->beginTransaction();
        $product->scenario = 'status';
        if(bcdiv($product->fazhi,1)){
            $total_money = OnlineFangkuan::find()->where(['online_product_id'=>$pid,'status'=>OnlineFangkuan::STATUS_FANGKUAN])->sum("order_money");//记录总的放款金额
            if(bcdiv(bcsub($product->money,bcadd($total_money,$optotal)),1)==0){
                $product->status =  OnlineProduct::STATUS_HUAN;//
            }else{
                $product->status =  OnlineProduct::STATUS_FOUND;//
            }
        }else{
            $product->status =  OnlineProduct::STATUS_HUAN;//
        }
        if(!$product->save()){
            $transaction->rollBack(); 
            return ['res'=>0,'msg'=>'标的状态更新失败'];
        }
        $mrecord = new MoneyRecord();
        $fee_rate = $product->fee;
        $total_in_money = 0;
        $ua = UserAccount::getUserAccount($product->borrow_uid,UserAccount::TYPE_RAISE);
        if(empty($ua)){
            $transaction->rollBack(); 
            return ['res'=>0,'msg'=>'用户融资账户异常'];
        }
        $fk = new OnlineFangkuan();
        $rmoney = $ua->available_balance;
        $time = time();
        $db = Yii::$app->db;
        foreach ($fkarr as $fkv){
            $fk = clone $fkv;
            $fk->status = OnlineFangkuan::STATUS_FANGKUAN;
            if(!$fk->save()){
                $transaction->rollBack(); 
                return ['res'=>0,'msg'=>'放款失败'];
            }
            $mre_model = clone $mrecord;
            $mre_model->type=  MoneyRecord::TYPE_FANGKUAN;
            $mre_model->sn = MoneyRecord::createSN();
            $mre_model->osn = $fk->sn;
            $mre_model->account_id = $ua->id;
            $mre_model->uid = $product->borrow_uid;
            $mre_model->in_money = $fk->order_money;
            $mre_model->remark = '已放款';
            $mre_model->balance = $bcround->bcround(bcadd($rmoney,$fk->order_money),2);
            $mre_model->status = MoneyRecord::STATUS_SUCCESS;
            if(!$mre_model->save()){
                $transaction->rollBack(); 
                var_dump(1,$mre_model->getErrors());exit;
                return ['res'=>0,'msg'=>'资金记录失败'];
            }
            $rmoney = $bcround->bcround(bcadd($rmoney,$fk->order_money),2);
            $total_in_money=bcadd($fk->order_money,$total_in_money);
            
            //获取批次详情订单数据
            $fkd = OnlineFangkuanDetail::find()->where(['fangkuan_order_id'=>$fk->id])->select('product_order_id')->asArray()->all();
            foreach($fkd as $fkdm){
                $resfkd = $db->createCommand('update online_repayment_plan set updated_at=:time where order_id=:oid', [
                    ':time' => $time,
                    ':oid' =>$fkdm['product_order_id']
                ])->execute();
                if(!$resfkd){
                    $transaction->rollBack(); 
                    return ['res'=>0,'msg'=>'修改放款时间错误'];
                }
            }
        }
        $fee = bcmul($total_in_money, $fee_rate);
        $total_in_money=bcsub($total_in_money,$fee);//计算扣取之后应得的部分
        
        $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance,$total_in_money),2);
        $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance,$total_in_money),2);
        $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum,$total_in_money),2);
        if($ua->available_balance*1<0){
            $transaction->rollBack(); 
            return ['res'=>0,'msg'=>'融资账户余额不足'];
        }
        if(!$ua->save()){
            $transaction->rollBack(); 
            return ['res'=>0,'msg'=>'更新用户融资账户异常'];
        }
        
        $mre_model = clone $mrecord;
        $mre_model->type=  MoneyRecord::TYPE_FEE;
        $mre_model->sn = MoneyRecord::createSN();
        $mre_model->osn = $product->sn;//通过这个osn判断是否已经扣过手续费
        $mre_model->account_id = $ua->id;
        $mre_model->uid = $product->borrow_uid;
        $mre_model->out_money = $fee;
        $mre_model->balance = $ua->account_balance;
        $mre_model->remark = '放款扣取手续费';
        $mre_model->status = MoneyRecord::STATUS_SUCCESS;
        if(!$mre_model->save()){
            $transaction->rollBack(); 
            var_dump(2,$mre_model->getErrors());exit;
            return ['res'=>0,'msg'=>'资金记录失败'];
        }
        
        
        $transaction->commit();
        return ['res'=>1,'msg'=>'操作成功']; 
    }
}
