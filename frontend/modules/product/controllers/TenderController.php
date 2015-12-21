<?php

namespace app\modules\product\controllers;

use Yii;
use yii\web\Controller;
use common\models\product\OnlineProduct;
use common\models\product\ProductCategory;
use common\models\product\ProductField;
use common\models\user\UserAccount;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder;
use common\models\contract\ContractTemplate;
use common\models\contract\Contract;
use common\models\order\OnlineRepaymentPlan;
use common\lib\product\ProductProcessor;
use common\lib\cfca\Payment;
use common\models\user\RechargeRecord;

class TenderController extends Controller {

    public function actionDetail($id = null) {
        $model = OnlineProduct::findOne($id);
        $ua_model = (\Yii::$app->user->isGuest) ? new UserAccount() : UserAccount::getUserAccount(\Yii::$app->user->id);
        $now = time();
        $start = $model->start_date;
        $end = $model->end_date;
        if ($model->status == OnlineProduct::STATUS_PRE) {
            $lefttime = $start - $now;
        } else if ($model->status == OnlineProduct::STATUS_NOW || $model->status == OnlineProduct::STATUS_FOUND) {
            $lefttime = $end - $now;
        } else {
            $lefttime = 0;
        }
        $allow = FALSE;//设置拒绝访问备查文件
        if(\Yii::$app->user->id){
            $user_model = \Yii::$app->user->getIdentity();
            //var_dump($user_model->type);
            if($user_model->type==2&&$user_model->examin_status==\common\models\user\User::EXAMIN_STATUS_PASS){//机构必须审核通过
                $allow=true;
            }else{
                $order = OnlineOrder::find()->where(['uid'=>$user_model->id,'online_pid'=>$id])->count();
                if($order||$user_model->idcard_status==\common\models\user\User::IDCARD_STATUS_PASS){
                    $allow=TRUE;//实名认证或持有产品才可以看
                }
            }
        }
        
        $cat_model = ProductCategory::findOne($model->cid);
        $balance = OnlineOrder::getOrderBalance($id); //计算商品可投余额
        $per = OnlineOrder::getRongziPercert($id); //计算融资百分比
        return $this->render("detail", [
                    'model' => $model,
                    "cat_model" => $cat_model,
                    'lefttime' => $lefttime,
                    "ua" => $ua_model,
                    "per" => $per,
                    'balance' => $balance,
                    'allow'=>$allow
        ]);
    }

    public function actionToOrder($id = null) {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }
        $created_time = time();
        $plan = OnlineProduct::findOne(['id' => $id]);
        if ($plan['start_date'] > $created_time || ( $plan['status'] != OnlineProduct::STATUS_NOW&& $plan['status'] != OnlineProduct::STATUS_FOUND)) {
            return $this->redirect('/product/tender/proerror?err=103');
        }
        if($plan->target){
            $uid = Yii::$app->user->id;
            $allow_uid = explode(',', $plan->target_uid);
            if(!in_array($uid, $allow_uid)){
                return $this->redirect('/product/tender/proerror?err=113');
            }
        }
        $price = Yii::$app->request->post('order_money');

        $pro = OnlineProduct::checkOnlinePro($id, \Yii::$app->user->id, $price);
        if ($pro != OnlineProduct::ERROR_SUCCESS) {
            return $this->redirect('/product/tender/proerror?err=' . $pro);
        }
        $tmptype = ContractTemplate::TYPE_TEMP_ONLINE;
        $ctemplate = ContractTemplate::getContractTemplateData($id, $tmptype);
        //var_dump($ctemplate);
        $model = OnlineProduct::findOne($id);
        $order = new OnlineOrder();
        $order->order_money = $price;
        $order->uid =  \Yii::$app->user->id;

        $session = Yii::$app->session;
        $sessionKey = \Yii::$app->user->id.'_is_sending';

        if ($order->load(Yii::$app->request->post()) && $order->validate()) {
            //存在$session[$sessionKey]正常提交
            if(isset($session[$sessionKey])){
                unset($session[$sessionKey]);//超过限制时间，释放session";
                $ua = UserAccount::getUserAccount(\Yii::$app->user->id);
                $balance = OnlineOrder::getOrderBalance($id); //计算标的剩余可投金额;
                $all_money = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $id])->sum('order_money');
                $transaction = Yii::$app->db->beginTransaction();
                $order->uid = Yii::$app->user->id;
                $order->sn = OnlineOrder::createSN();
                //$order->drawpwd = \Yii::$app->security->generatePasswordHash($this->drawpwd);
                $order->online_pid = $id;
                $order->order_time = time();
                $order->refund_method = $model->refund_method;
                $order->yield_rate = $model->yield_rate;
                $order->expires = $model->expires;
                $ore = $order->save();
                if (!$ore) {
                    $transaction->rollBack();
                    return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_SYSTEM);
                }
                //用户资金表
                $ua->available_balance = bcsub($ua->available_balance, $price, 2);
                if ($ua->available_balance * 1 < 0) {
                    $transaction->rollBack();
                    return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_MONEY_LESS);
                }
                $ua->freeze_balance = bcadd($ua->freeze_balance, $price, 2);
                $ua->out_sum = bcadd($ua->out_sum, $price, 2);
                $uare = $ua->save();
                if (!$uare) {
                    $transaction->rollBack();
                    return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_SYSTEM);
                }
                //资金记录表
                $mrmodel = new MoneyRecord();
                $mrmodel->account_id = $ua->id;
                $mrmodel->sn = MoneyRecord::createSN();
                $mrmodel->type = MoneyRecord::TYPE_ORDER;
                $mrmodel->osn = $order->sn;
                $mrmodel->status = 1;
                $mrmodel->uid = Yii::$app->user->id;
                $mrmodel->balance = $ua->available_balance;
                $mrmodel->out_money = $price;
                $mrmodel->remark = "资金流水号:" . $mrmodel->sn . ',订单流水号:' . ($order->sn) . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                $mrres = $mrmodel->save();
                if (!$mrres) {
                    $transaction->rollBack();
                    return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_SYSTEM);
                }

                //生成合同 start
                $contract = new Contract();
                foreach ($ctemplate as $val) {
                    $c_model = clone $contract;
                    $c_model->order_id = $order->id;
                    $c_model->contract_name = $val->name;
                    $c_model->contract_number = $order->sn;
                    $c_model->contract_template_id = $val->id;
                    if($val->type==3){
                        $c_model->contract_content = Contract::replaceOnlineContract($val->content,$order,\Yii::$app->user->getIdentity());
                    }else{
                        $c_model->contract_content = $val->content;
                    }
                    $c_model->path = $val->path;
                    $c_model->type = $tmptype;
                    $c_model->order_sn = $order->sn;
                    $cre = $c_model->save();
                    if (!$cre) {
                        $transaction->rollBack();
                        return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_CONTRACT);
                    }
                }
                //生成合同 end            
                if ($model->fazhi / 1) {//如果设置阀值
                    $all_money = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $id])->sum('order_money');
                    if (bcsub($all_money, $model->fazhi, 2) / 1 >= 0) {
                        $model->scenario = 'status';
                        $model->status = OnlineProduct::STATUS_FOUND;
                        $opre = $model->save();
                        if (!$opre) {
                            $transaction->rollBack();
                            return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_PRO_STATUS);
                        }
                    }
                }

                if (bcdiv(bcsub($balance, $price), 1) == 0) {
                    $bool_full = TRUE;
                    $model->scenario = 'status';
                    $model->status = OnlineProduct::STATUS_FULL;
                    $opre = $model->save();
                    if (!$opre) {
                        $transaction->rollBack();
                        return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_PRO_STATUS);
                    }
                }
                $transaction->commit();
                ////OnlineRepaymentPlan::createPlan($id);//因为还款计划含有审核功能，所以，还款计划放置于审核时候生成
                return $this->redirect('/product/tender/agreement');
            }else{
                //return $this->redirect('/product/tender/proerror?err=' . OnlineProduct::ERROR_SYSTEM);
                return $this->redirect('/product/tender/agreement');//有可能重复提交
            }
        }
        
        if(!isset($session[$sessionKey])){
            $session[$sessionKey] = time();
        }
        //echo $session[$sessionKey];
        return $this->render("tender-agreement", ['model' => $model, 'omodel' => $order, 'money' => $price, 'ecript_id' => ($id), 'ctemplate' => $ctemplate]);
    }

    public function actionProerror($err = 101) {
        $errmsg = OnlineProduct::getErrorByCode($err);
        return $this->render("tender-defeat", ['msg' => $errmsg]);
    }

    public function actionAgreement() {
        return $this->render("tender-success");
        //return $this->render("tender-defeat");
        //return $this->render("tender-agreement");
        //return $this->render("detail");
        //return $this->render("tender-agreement");
    }

    /**
     * 合同视图页面
     * @param type $p 产品id
     * @param type $t 类型
     * @return type
     */
    public function actionHetongview($p = null, $t = null, $h = null, $op = "I") {
        if (empty($p) || empty($t) || empty($h)) {
            return $this->redirect('/');
        }
        $hetong = ContractTemplate::findOne($h);
        if ($hetong->pid != $p || $hetong->type != $t || !in_array($op, ["I", "D"])) {
            exit("合同类型错误");
        }
        Yii::$app->functions->createHetong($hetong->name, $hetong->content, time(), $op);
    }
    
    public function actionPreview($id = null){
        $hetong = ContractTemplate::findOne($id);
        if($hetong->type!=3){
            return "合同类型有误";
        }
        return $hetong->content;
    }

    /**
     * 中金20分钟一次，一共补发5次
     */
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    public function actionRechargebackcallback() {

        $filename = \Yii::getAlias('@frontend') . "/web/text.txt";
        $content = file_get_contents($filename) . '\r\n';
        $fp = fopen($filename, "w"); //文件被清空后再写入 

        $message = \Yii::$app->request->post('message');
        $signature = Yii::$app->request->post("signature");
        $payment = new Payment();

        $responseXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmlNotification']);
        $plainText = trim(base64_decode($message));
        $ok = $payment->cfcaverify($plainText, $signature);
        $content .= '';
        if ($ok != 1) {
            $errInfo = "验签失败";
            $responseXML->Head->Code = "2001";
            $responseXML->Head->Message = $errInfo;
            $content .= $errInfo;
        } else {
            $txName = "";
            $simpleXML = new \SimpleXMLElement($plainText);
            $txCode = $simpleXML->Head->TxCode;
            if ($txCode == "1118") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单支付状态变更通知";
            } else if ($txCode == "1119") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单支付状态变更通知";
            } else if ($txCode == "1138") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单退款结算状态变更通知";
            } else if ($txCode == "1318") {
                $InstitutionID = $simpleXML->Body->InstitutionID; //获取返回的机构编号
                //$content .= empty($simpleXML->Body->InstitutionID)?"kong":$simpleXML->Body->InstitutionID;
                if ($InstitutionID != \Yii::$app->params['cfca']['InstitutionID']) {
                    
                } else {
                    $Status = intval($simpleXML->Body->Status); //获取返回结果 状态： 10=未支付 20=已支付
                    $BankNotificationTime = $simpleXML->Body->BankNotificationTime; //获取返回支付平台收到银行通知时间
                    $Amount = $simpleXML->Body->Amount; //支付金额，单位：分
                    $PaymentNo = $simpleXML->Body->PaymentNo; //支付交易流水号
                    $rechareg = RechargeRecord::findOne(['sn' => $PaymentNo]);
                    if (empty($rechareg)) {
                        //$content .= ('不正确的充值单据[' . $PaymentNo . ']');
                        $responseXML->Head->Code = "2001";
                        $responseXML->Head->Message = '不正确的充值单据';
                    } else if ($rechareg->status == RechargeRecord::STATUS_YES) {
                        $responseXML->Head->Code = "2000";
                        $responseXML->Head->Message = "OK";
                    } else if ($Status == 20) {
                        $uid = $rechareg->uid;
                        //$ua = UserAccount::getUserAccount($uid);
                        $ua = UserAccount::findOne($rechareg->account_id);//以当前充值记录需要冲到的账户进行充值
                        $bcround = new \common\lib\bchelp\BcRound();
                        $jiesuan = new \common\models\user\Jiesuan();
                        $cfcalog = new \common\models\user\CfcaLog();
                        bcscale(14);
                        $transaction = Yii::$app->db->beginTransaction();
                        
                        $cfcalog->type=3;
                        $cfcalog->account_id=$ua->id;
                        $cfcalog->uid=$uid;
                        $cfcalog->log_type=2;
                        $cfcalog->response=$plainText;

                        if(!$cfcalog->save()){
                            $transaction->rollBack();
                            $content .= ('cfca状态异常');
                        }
                    
                        $rechareg->status = RechargeRecord::STATUS_YES;
                        $rechareg->bankNotificationTime = $BankNotificationTime;
                        if (!$rechareg->save()) {
                            $transaction->rollBack();
                            $content .= ('修改充值状态异常');
                        }

                        $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        if (!$ua->save()) {
                            $transaction->rollBack();
                            $content .= ('资金记录异常');
                        }

                        $mr_model = new MoneyRecord();
                        $mr_model->sn = MoneyRecord::createSN();
                        $mr_model->osn = $PaymentNo;
                        $mr_model->type = MoneyRecord::TYPE_RECHARGE;
                        $mr_model->account_id = $ua->id;
                        $mr_model->uid = $uid;
                        $mr_model->balance = $ua->available_balance;
                        $mr_model->remark = "资金流水号:" . $mr_model->sn . ',充值流水号:' . $PaymentNo . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                        $mr_model->status = MoneyRecord::STATUS_SUCCESS;
                        $mr_model->in_money = bcdiv($Amount, 100, 2) * 1;
                        $mrre = $mr_model->save();
                        //var_dump($mr_model->getErrors());
                        if (!$mrre) {
                            $transaction->rollBack();
                            $content .= ('资金记录异常');
                        }
                        $jiesuan->amount=bcdiv($Amount, 100, 2) * 1;
                        $jiesuan->osn=$rechareg->sn;
                        $jiesuan->bankNotificationTime=$BankNotificationTime;
                        $res = $jiesuan->settlement();
                        if($res===FALSE){
                            $transaction->rollBack();
                        }
                        $transaction->commit();
                    }
                }
                //以下为演示代码
                $txName = "市场订单支付状态变更通知";
            } else if ($txCode == "1348") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "市场订单结算状态变更通知";
            } else if ($txCode == "1363") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "市场订单单笔代收结果通知";
            } else if ($txCode == "1712") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权成功结果通知";
            } else if ($txCode == "1722") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权撤销结果通知";
            } else if ($txCode == "1732") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权扣款结果通知";
            } else if ($txCode == "2018") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "实时代扣结果通知";
            } else if ($txCode == "3218") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "P2P支付成功通知（托管户）";
            } else if ($txCode == "4233") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户注册成功通知";
            } else if ($txCode == "4243") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户银行账户绑定成功通知（托管户）";
            } else if ($txCode == "4247") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户银行账户解绑成功通知（托管户）";
            } else if ($txCode == "4253") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户充值成功通知（托管户）";
            } else if ($txCode == "4257") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户提现成功通知（托管户）";
            } else if ($txCode == "4263") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户扣款签约成功通知（托管户）";
            } else {
                $txName = "未知通知类型";
            }
            $content .= $txCode . "_" . $txName;
            $responseXML->Head->Code = "2000";
            $responseXML->Head->Message = "OK";
        }

        // 商户自身逻辑处理完成之后,需要向支付平台返回响应
        $responseXMLStr = $responseXML->asXML();
        $base64Str = base64_encode(trim($responseXMLStr));
        if ($fp) {
            $flag = fwrite($fp, $content . $base64Str);
            echo "写入字符";
        } else {
            echo "打开文件失败";
        }
        fclose($fp);
        print $base64Str;
//        exit;
//          HttpResponse::status(200);
//          HttpResponse::setContentType('text/plain');
//          HttpResponse::setData($base64Str);
//          HttpResponse::send();
    //
    }

    public function actionCrontab(){
        $crontab = new \common\lib\crontab\Crontab();
        $crontab->updateOlproStatus();
    }
    
    public function actionTc(){
        echo Contract::change_num(589340000);
//        $ct = ContractTemplate::findOne(39);
//        $order = OnlineOrder::findOne(89);
//        $contract = Contract::replaceOnlineContract($ct->content,$order,\Yii::$app->user->getIdentity());
        //var_dump($contract);
    }

}
