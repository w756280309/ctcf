<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\models\city\Region;
use common\models\user\DrawRecord;
use common\models\user\EditpassForm;
use common\models\user\MoneyRecord;
use common\models\user\RechargeRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\user\UserBanks;
use common\service\BankService;
use common\service\SmsService;
use common\service\UserService;
use Yii;
use yii\web\Response;

class UserbankController extends BaseController {
    //实名认证表单页
    public function actionIdcardrz() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_Y;
        $data = BankService::check($this->uid,$cond);
        if($data[code] == 1) {
            if(Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('idcardrz',$data);
            }
        }

        $model = $data['user'];
        $model->scenario = 'idcardrz';
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->idcard_status = User::IDCARD_STATUS_PASS;
            if($model->save()) {
                //实名认证成功
                return ['tourl' => '/user/userbank/bindbank','code' => 0, 'message' => '实名认证成功'];
            }
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }
        return $this->render('idcardrz');
    }

    //绑定银行卡表单页
    public function actionBindbank() {
        $uid = $this->uid;
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_Y;
        $data = BankService::check($uid,$cond);
        if($data['code'] == 1) {
            if(Yii::$app->request->isAjax) {
                return $data;
            } else {
                $arr = array();
                return $this->render('bindbank', ['banklist' => $arr, 'data' => $data]);
            }
        }

        $user = $data['user'];
        $model = new UserBanks();
        $model->scenario = 'step_first';
        $model->uid = $uid;
        $model->account = $user->real_name;
        $model->account_type = UserBanks::PERSONAL_ACCOUNT;
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->status = UserBanks::STATUS_YES;
            if($model->save())
            {
                //绑卡成功
                $res = SmsService::editSms($uid);
                return ['tourl' => '/user/userbank/addbuspass','code' => 1, 'message' => '绑卡成功'];
            }
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        $arr = array();
        $bank = Yii::$app->params['bank'];
        $i=0;
        foreach($bank as $key => $val)
        {
            $arr[$i] = array('id'=>$key,'bankname' => $val['bankname'], 'image' => $val['image']);
            $i++;
        }

        return $this->render('bindbank', ['banklist' => $arr]);
    }

    //设置交易密码表单页
    public function actionAddbuspass() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_Y;
        $data = BankService::check($this->uid,$cond);
        if($data[code] == 1) {
            if(Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('addbuspass', $data);
            }
        }

        $model = new EditpassForm();
        $model->scenario = 'add';
        if($model->load(Yii::$app->request->post())) {
            if($model->editpass()) {
                return ['tourl' => '/user/user','code' => 1, 'message' => '添加交易密码成功'];
            }
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('addbuspass');
    }

    //修改交易密码表单页
    public function actionEditbuspass() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $model = new EditpassForm();

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;
        $data = BankService::check($this->uid,$cond);
        if($data[code] == 1) {
            if(Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('editbuspass',['model'=>$model, 'data' => $data]);
            }
        }

        $model->scenario = 'edit';
        if($model->load(Yii::$app->request->post())) {
            if($model->editpass()) {
                return ['tourl' => '/user/user','code' => 1, 'message' => '交易密码修改成功'];
            }
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('editbuspass',['model'=>$model]);
    }
    
    /**
     * 快捷支付
     */
    public function actionRecharge(){
        //\Yii::$app->session->remove('cfca_qpay_recharge');
        $this->layout = "@app/modules/order/views/layouts/buy";
        $uid = $this->uid;
        $user = User::findOne($uid);
        if($user && $user->status == User::STATUS_DELETED) {
            $this->redirect('/site/usererror');
        }
        $user_bank = UserBanks::find()->where(['uid' => $uid])->select('id,binding_sn,bank_id,bank_name,card_number,status')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_BUY, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();
        if($user_acount->in_sum == 0) {
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;
            $data = BankService::check($uid,$cond);
            if($data[code] == 1) {
                return $this->render('recharge',['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
            }
        }
//        $pending = Yii::$app->session->get('cfca_qpay_recharge');
//        var_dump($pending);
        if(\Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $pending = Yii::$app->session->get('cfca_qpay_recharge');
            $sms = \Yii::$app->request->post('yzm');
            if($pending===null){
                return $this->createErrorResponse('请先发送短信码');
            }else{
                $recharge = RechargeRecord::find()->where(['sn'=>$pending['recharge_sn']])->one();
                //var_dump($recharge,$pending['recharge_sn']);exit;
                if(empty($recharge)||$recharge->status!=0){
                    return $this->createErrorResponse('支付异常');
                }
                if (
                        bccomp($recharge->fund , $pending['recharge_fund'])!=0
                ) {
                    return $this->createErrorResponse('支付金额已经修改，请重新请求短信验证码');
                }
                $ret = $this->rechargecheckpay($recharge,$sms);
                if($ret['code']===0){
                    \Yii::$app->session->remove('cfca_qpay_recharge');
                    return [
                    'next' => $ret['tourl'],
                ];
                }else{
                    return $this->createErrorResponse($ret['message']);
                }                
            }
        }
        return $this->render('recharge',['user_bank' => $user_bank, 'user_acount' => $user_acount]);
    }
    
    /**
     * 可提供公用的函数
     * @param $modelOrMessage
     * @return type
     */
    private function createErrorResponse($modelOrMessage = null)
    {
        Yii::$app->response->statusCode = 400;
        $message = null;

        if (is_string($modelOrMessage)) {
            $message = $modelOrMessage;
        } elseif (
            $modelOrMessage instanceof Model
            && $modelOrMessage->hasErrors()
        ) {
            $message = current($modelOrMessage->getFirstErrors());
        }

        return [
            'message' => $message,
        ];
    }
    
    /**
     * 获取快捷支付短信码
     */
    public function actionGetpaysms() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $uid = $this->uid;
        $user = User::findOne($uid);
        if($user && $user->status == User::STATUS_DELETED) {
            return $this->createErrorResponse('用户被禁止访问');
        }

        $user_bank = UserBanks::find()->where(['uid' => $uid])->select('id,binding_sn,bank_id,bank_name,card_number,status')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_BUY, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();

        if($user_acount->in_sum == 0) {
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N;
            $data = BankService::check($uid,$cond);
            if($data[code] == 1) {
                return $this->createErrorResponse($data['message']);
            }
        }

        $recharge = new RechargeRecord();
        $recharge->bank_id = "$user_bank->bank_id";
        $recharge->uid = $uid;
        
        if($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            $recharge->sn = RechargeRecord::createSN();
            $transaction = Yii::$app->db->beginTransaction();
            //录入recharge_record记录
            $recharge->pay_id = 0;
            $recharge->account_id = $user_acount->id;
            $recharge->pay_bank_id = strval($user_bank->bank_id);
            $recharge->bankNotificationTime = '0';
            $recharge->status = RechargeRecord::STATUS_NO;
            //var_dump($recharge);exit;
            if(!$recharge->save()) {
                $transaction->rollBack();
                return $this->createErrorResponse('充值失败');
            }
            
            $xml_path = Yii::getAlias('@common')."/config/xml/cfca_1375.xml";
            $xmltx1375 = file_get_contents($xml_path);
            $InstitutionID = \Yii::$app->params['cfca']['institutionId'];
            $simpleXML = new \SimpleXMLElement($xmltx1375);
            $simpleXML->Head->InstitutionID = $InstitutionID;
            $simpleXML->Body->OrderNo = $recharge->sn;
            $simpleXML->Body->PaymentNo = $recharge->sn;
            $simpleXML->Body->TxSNBinding = $user_bank->binding_sn;
            $simpleXML->Body->Amount = $recharge->fund*100;

            $payment = new \common\lib\cfca\Payment();
            $xmlStr = $simpleXML->asXML();
            $message = base64_encode(trim($xmlStr));
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $response = $payment->cfcatx_transfer($message, $signature);
            $plainText = (base64_decode($response[0]));//去掉了前边的trim
            $ok = $payment->cfcaverify($plainText, $response[1]); 
            if($ok!=1){
                $transaction->rollBack();
                return $this->createErrorResponse('充值失败,验签失败');               
            }else{
                $response_XML= new \SimpleXMLElement($plainText);
                if($response_XML->Head->Code == "2000"){
                    $transaction->commit();
                    // 调用 存session 防止修改
                    Yii::$app->session->set('cfca_qpay_recharge', [
                        'recharge_sn' => $recharge->sn,
                        'recharge_fund' => $recharge->fund,
                        '_time' => time(),
                    ]);
                    return ['rechargeSn' => $recharge->sn];
                }else{
                    $transaction->rollBack();
                    return $this->createErrorResponse($response_XML->Head->Message);      
                }
            }
        }

        if($recharge->getErrors()) {
            $message = $recharge->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('recharge',['user_bank' => $user_bank, 'user_acount' => $user_acount]);
    }


    /**
     * 快捷支付输入验证码【20151222取消使用】
     */
//    public function actionRechargepay($s=null){
//        exit;
//        if(!empty($s)){
//            $recharge = RechargeRecord::find()->where(['sn'=>$s])->one();
//            if(!empty($recharge)&&$recharge->status==RechargeRecord::STATUS_NO){
//                $this->layout = "@app/modules/order/views/layouts/buy";      
//                return $this->render('rechargepay',['recharge' => $recharge]);
//            }  else {
//                //错误页面
//            }
//        }else{
//            //错误页面
//        }
//    }
    
    /**
     * $recharge 充值数据
     * $yzm 中金短信
     * 快捷支付输入验证码验证支付短信码
     */
    public function rechargecheckpay($recharge,$yzm){
        $payment = new \common\lib\cfca\Payment();
        $xml = Yii::getAlias('@common')."/config/xml/cfca_1376.xml";
        $content = file_get_contents($xml);  
        $simpleXML = new \SimpleXMLElement($content);
        $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['institutionId'];//测试
        $simpleXML->Body->OrderNo = $recharge->sn;
        $simpleXML->Body->PaymentNo = $recharge->sn;
        $simpleXML->Body->SMSValidationCode = $yzm;
        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = (base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
        if($ok!=1){
            return ['code' => 1, 'message' => '充值失败,验签失败'];
        }else{
            $response_XML= new \SimpleXMLElement($plainText);
            if($response_XML->Head->Code == "2000"){
                if($response_XML->Body->Status==20){
                    $bankTxTime = $response_XML->Body->BankTxTime;
                    $uid = $this->uid;
                    $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_BUY, 'uid' => $uid])->select('id,uid,in_sum,available_balance')->one();
                    //录入money_record记录
                    $transaction = Yii::$app->db->beginTransaction();
                    RechargeRecord::updateAll(['status'=>1,'bankNotificationTime'=>$bankTxTime],['id'=>$recharge->id]);
                    $bc = new BcRound();
                    bcscale(14);
                    $money_record = new MoneyRecord();
                    $money_record->sn = MoneyRecord::createSN();
                    $money_record->type = MoneyRecord::TYPE_RECHARGE;
                    $money_record->osn = $recharge->sn;
                    $money_record->account_id = $user_acount->id;
                    $money_record->uid = $uid;
                    $money_record->balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund),2);
                    $money_record->in_money = $recharge->fund;
                    $money_record->status = MoneyRecord::STATUS_SUCCESS;

                    if(!$money_record->save()) {
                        $transaction->rollBack();
                        return ['code' => 1, 'message' => '充值失败'];
                    }

                    //录入user_acount记录
                    $user_acount->uid = $user_acount->uid;
                    $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $recharge->fund),2);
                    $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund),2);
                    $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $recharge->fund),2);

                    if(!$user_acount->save()) {
                        $transaction->rollBack();
                        return ['code' => 1, 'message' => '充值失败'];
                    }

                    $transaction->commit();
                    return ['tourl' => '/user/user', 'code' => 0, 'message' => '充值成功'];
                }else{
                    return ['code' => 1, 'message' => '充值失败'];//包含处理中和充值失败
                }
            }else{
                return ['code' => 1, 'message' => $response_XML->Head->Message];       
            }
        }
    }


    public function actionTixian() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $uid = $this->uid;
        $user = User::findOne($uid);
        if($user && $user->status == User::STATUS_DELETED) {
            $this->redirect('/site/usererror');
        }

        $user_bank = UserBanks::find()->where(['uid' => $uid, 'status' => UserBanks::STATUS_YES])->select('bank_id,bank_name,account,card_number')->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_BUY, 'uid' => $uid])->select('available_balance')->one();

        if($user_acount->out_sum == 0) {
            $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N | BankService::CHARGEPWD_VALIDATE_N | BankService::EDITBANK_VALIDATE;
            $data = BankService::check($uid,$cond);
            if($data[code] == 1) {
                if (Yii::$app->request->isAjax) {
                    return $data;
                } else {
                    return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount, 'data' => $data]);
                }
            }
        }

        $draw = new DrawRecord();
        $draw->uid = $uid;
        if($draw->load(Yii::$app->request->post()) && $draw->validate()) {
            $us = new UserService();
            $re = $us->checkDraw($uid, $draw->money);
            if($re['code']) {
                return $re;
            }

            return ['tourl' => '/user/userbank/checktradepwd?money='.$draw->money, 'code' => 0, 'message' => ''];
        }

        if($draw->getErrors()) {
            $message = $draw->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('tixian', ['user_bank' => $user_bank, 'user_acount' => $user_acount]);
    }

    public function actionChecktradepwd($money=null) {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $uid = $this->uid;
        $user = User::findOne($uid);
        if($user && $user->status == User::STATUS_DELETED) {
            $this->redirect('/site/usererror');
        }
        
        $draw = new DrawRecord();
        $draw->uid = $uid;
        $draw->money = $money;
        $data = '';
        if($draw->validate()) {
            $us = new UserService();
            $re = $us->checkDraw($uid, $draw->money);
            if($re['code']) {
                $data = ['tourl' => '/user/userbank/tixian', 'code' => $re['code'], 'message' => $re['message']];
            }
        } else {
            $message = $draw->firstErrors;
            $data = ['tourl' => '/user/userbank/tixian', 'code' => 1, 'message' => current($message)]; 
        }
        
        if(!empty($data)) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                return $this->render('checktradepwd', ['status' => 0, 'data' => $data]);
            }        
        }

        $user_bank = UserBanks::find()->where(['uid' => $uid])->one();
        $user_acount = UserAccount::find()->where(['type' => UserAccount::TYPE_BUY, 'uid' => $uid])->one();

        $model = new EditpassForm();
        $model->scenario = 'checktradepwd';
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            $money_r = Yii::$app->request->post('money');
            if($money != $money_r) {
                return $this->redirect('/user/userbank/tixian');
            }
            
            $transaction = Yii::$app->db->beginTransaction();
            //录入draw_record记录
            $draw = new DrawRecord;
            $draw->money = $money;
            $draw->sn = DrawRecord::createSN();
            $draw->pay_id = 0;
            $draw->account_id = $user_acount->id;
            $draw->uid = $uid;
            $draw->pay_bank_id = '0';
            $draw->bank_id = $user_bank->bank_id;
            $draw->bank_username = $user_bank->bank_name;
            $draw->bank_account = '0';
            $draw->status = DrawRecord::STATUS_ZERO;

            if(!$draw->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现失败'];
            }

            //录入money_record记录
            $bc = new BcRound();
            bcscale(14);
            $money_record = new MoneyRecord();
            $money_record->sn = MoneyRecord::createSN();
            $money_record->type = MoneyRecord::TYPE_DRAW;
            $money_record->osn = $draw->sn;
            $money_record->account_id = $user_acount->id;
            $money_record->uid = $uid;
            $money_record->balance = $bc->bcround(bcsub($user_acount->available_balance, $draw->money),2);
            $money_record->out_money = $draw->money;
            $money_record->status = MoneyRecord::STATUS_ZERO;

            if(!$money_record->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现失败'];
            }

            //录入user_acount记录
            $user_acount->uid = $user_acount->uid;
            $user_acount->available_balance = $bc->bcround(bcsub($user_acount->available_balance, $draw->money),2);
            $user_acount->freeze_balance = $bc->bcround(bcadd($user_acount->freeze_balance, $draw->money),2);
            //$user_acount->out_sum = $bc->bcround(bcadd($user_acount->out_sum, $draw->money),2);

            if(!$user_acount->save()) {
                $transaction->rollBack();
                return ['code' => 1, 'message' => '提现失败'];
            }

            $transaction->commit();
            return ['tourl' => '/user/user', 'code' => 1, 'message' => '提现成功'];
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        return $this->render('checktradepwd',['money' => $money]);
    }

    public function actionEditbank() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
        }

        $cond = 0 | BankService::IDCARDRZ_VALIDATE_N | BankService::BINDBANK_VALIDATE_N;
        $data = BankService::check($this->uid,$cond);
        $model = $data['user_bank'];
        if($data['code'] == 1) {
            if (Yii::$app->request->isAjax) {
                return $data;
            } else {
                $province = array();
                return $this->render('editbank',['model' => $model, 'province' => $province, 'data' => $data]);
            }
        }

        $model->scenario = 'step_second';
        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            if($model->save()) {
                return ['tourl' => '/user/userbank/tixian','code' => 0, 'message' => '银行信息完善成功'];
            }
        }

        if($model->getErrors()) {
            $message = $model->firstErrors;
            return ['code' => 1, 'message' => current($message)];
        }

        $province = Region::find()->where(['province_id'=>0])->select('id,name')->asArray()->all();
        $data = ['tourl' => '', 'code' => 0, 'message' => '修改成功'];

        return $this->render('editbank',['model' => $model, 'province' => $province, 'data' => $data]);
    }

    public function actionCheckbank() {
        $card = Yii::$app->request->post('card');
        $result = BankService::checkBankcard($card);
        return $result;
    }

    public function actionBankxiane()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('bankxiane');
    }

    public function actionKuaijie()
    {
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('kuaijie');
    }

}
