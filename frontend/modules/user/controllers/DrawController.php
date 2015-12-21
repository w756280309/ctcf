<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use yii\web\Response;
use common\models\user\User;
use common\models\user\DrawRecord;
use common\models\user\UserAccount;
use frontend\controllers\BaseController;
use common\models\user\MoneyRecord;
use common\models\user\UserBank;
use common\models\user\UserBanks;
use common\models\user\Batchpay;
use common\models\sms\SmsTable;
use common\models\user\CfcaLog;

class DrawController extends BaseController {

    public $layout = 'main';

    /**
     * 提现申请
     */
    public function actionWithdrawcash() {
        $uid = $this->uid;
        $session = Yii::$app->session;
        $useraccount = $session->get('useraccount');
        $ua = UserAccount::getUserAccount($uid, $useraccount);
        $available_balance = $ua->available_balance;
        //$ubank = new UserBank();
        $banks = UserBank::find()->where(['uid' => $uid])->all();
        $model = new DrawRecord();
        $model->uid = $uid;
        $model->account_id = $ua->id;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $bank = UserBank::findOne(['id' => $model->bank_id, 'uid' => $uid]);
            $bankstatus = $bank->status;
            $model->bank_username = $bank->bank_name;
            $model->bank_account = $bank->card_number;

            $ua = UserAccount::getUserAccount($uid, $useraccount);
            $available_balance = $ua->available_balance;

            if (empty($bank)) {
                $model->addError('bank_id', '无效的银行卡');
            } elseif (!$bankstatus == UserBank::STATUS_YES) {
                return $this->redirect('/user/draw/addtip?status=wait');
            } elseif (bcdiv($available_balance, 1, 2) * 1 == 0) {
                $model->addError('money', '余额不足');
            } else if (bcsub($available_balance, $model->money, 4) * 1 < 0) {
                $model->addError('money', '余额不足');
            } else {
                //$transaction = Yii::$app->db->beginTransaction();
                $ua->available_balance = bcsub($ua->available_balance, $model->money, 2);
                $ua->freeze_balance = bcadd($ua->freeze_balance, $model->money, 2);
                $ua->out_sum = bcadd($ua->out_sum, $model->money, 2);
                $ure = $ua->save();

                $model->sn = DrawRecord::createSN();
                $dre = $model->save();

                $mr_model = new MoneyRecord();
                $mr_model->sn = MoneyRecord::createSN();
                $mr_model->osn = $model->sn;
                $mr_model->type = MoneyRecord::TYPE_DRAW;
                $mr_model->account_id = $ua->id;
                $mr_model->uid = $uid;
                $mr_model->out_money = $model->money;
                $mr_model->balance = $ua->available_balance;
                $mr_model->status = MoneyRecord::STATUS_ZERO;
                $mr_model->remark = "资金流水号:" . $mr_model->sn . ',提现流水号:' . ($model->sn) . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                $mrre = $mr_model->save();

                if ($dre && $mrre && $ure) {
                    //$transaction->commit();
                    return $this->redirect('/user/draw/drawstatus?status=success');
                } else {
                    //$transaction->rollBack();
                    return $this->redirect('/user/draw/drawstatus?status=defeat');
                }
            }
        }

        return $this->render('withdrawcash', ['model' => $model, 'available_balance' => $available_balance, 'banks' => $banks]);
    }

    /**
     * 设置交易密码
     */
    public function actionSettradepwd($res = null) {
        $this->layout = false;
        if (\Yii::$app->user->isGuest) {
            exit("无权进入页面");
        }
        $model = \Yii::$app->user->identity;
        $old_trade_pwd = $model->trade_pwd;
        if (empty($old_trade_pwd)) {
            $model->scenario = "settrade";
        } else {
            $model->scenario = "uptrade";
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($old_trade_pwd) {
                $bool = $model->validateTradePwd($model->old_trade, $old_trade_pwd);
                if ($bool) {
                    $model->trade_pwd = $model->setTradePassword($model->new_trade);
                    if ($model->save()) {
                        return $this->redirect('/user/draw/settradepwd?res=100');
                    } else {
                        return $this->redirect('/user/draw/settradepwd?res=101');
                    }
                } else {
                    return $this->redirect('/user/draw/settradepwd?res=102');
                }
            } else {
                $model->setAttribute('trade_pwd', $model->setTradePassword($model->f_trade_pwd));
                //$model->setAttribute('trade_pwd', md5($model->f_trade_pwd));
                $bool = $model->save();
                if ($bool) {
                    return $this->redirect('/user/draw/settradepwd?res=100');
                } else {
                    return $this->redirect('/user/draw/settradepwd?res=101');
                }
            }
        }
        return $this->render('settradepwd', ['model' => $model, 'res' => $res, 'set' => empty($old_trade_pwd) ? 0 : 1]);
    }

    /**
     * 提现添加银行卡——审核中
     */
    public function actionAddtip() {
        $status = add;
        //$status = wait;
        return $this->render('addtip', ['status' => $status]);
    }

    /**
     * 提现绑定银行卡
     */
    public function actionBindcard() {
        $this->layout = 'login';
        $bank_show = Yii::$app->params['bank'];
        foreach ($bank_show as $key => $val) {
            if ($val['status'] == '0') {
                unset($bank_show[$key]);
            }
        }
        $model = new UserBank();
        $model->uid = $this->uid;
        $start_time = strtotime(date('Y-m-d') . ' 0:00:00');
        $end_time = strtotime(date('Y-m-d') . ' 23:59:59');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $bind_count = UserBank::find()->where(['uid' => $this->uid])->andFilterWhere(['>', 'created_at', $start_time])->andFilterWhere(['<', 'created_at', $end_time])->count('id');
            if ($bind_count >= UserBank::BIND_COUNT) {
                $model->addError('account', '今日绑卡数已经超过' . UserBank::BIND_COUNT . '次，请明日重试');
            } else {
                $transaction = Yii::$app->db->beginTransaction();
                $res = $model->save();
                if ($res) {
                    $bp = new Batchpay();
                    $bp->addBatchpay($model);
                    $transaction->commit();
                    return $this->redirect('/user/draw/bindaudit');
                } else {
                    $transaction->rollBack();
                }
                return $this->redirect('/user/draw/bindstatus?status=defeat');
            }
        }
        return $this->render('bindcard', ['model' => $model, 'bank_show' => $bank_show]);
    }

    /**
     * 提现绑定银行卡审核中
     */
    public function actionBindaudit() {
        $this->layout = 'login';
        return $this->render('bindaudit');
    }

    /**
     * 提现绑定银行卡——是否成功
     */
    public function actionBindstatus($status = 'defeat') {
        $this->layout = login;
        //$status = success;
        //$status = defeat;
        return $this->render('bindstatus', ['status' => $status]);
    }

    /**
     * 提现——是否成功
     */
    public function actionDrawstatus($status = 'defeat') {
        $this->layout = login;
        //$status = success;
        //$status = defeat;
        return $this->render('drawstatus', ['status' => $status]);
    }

    /* 0928 新版绑卡 start */

    /**
     * 提现绑定银行卡
     */
    public function actionBindcardnew($id = null) {
        if($this->user->mobile_status!=1||$this->user->idcard_status!=1||$this->user->email_status!=1){
            //return $this->redirect('/user/draw/bindstatus?status=certification');
        }
        //echo strpos('1天','天1');
        $this->layout = 'login';
        $bank_show = Yii::$app->params['bank'];
        foreach ($bank_show as $key => $val) {
            if ($val['status'] == '0') {
                unset($bank_show[$key]);
            }
        }

        $model = $id ? UserBanks::findOne($id) : new UserBanks();
        $model->scenario = $id ? "step_second" : 'step_first';
        $model->uid = $this->uid;
        $start_time = strtotime(date('Y-m-d') . ' 0:00:00');
        $end_time = strtotime(date('Y-m-d') . ' 23:59:59');
        $mobile = substr_replace($this->user->mobile, '****', 3, 4);
        $idcard = ($this->user->type == 1) ? $this->user->idcard : $this->user->law_master_idcard;
        $idcard = substr_replace($idcard, '************', 3, 11);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $bind_count = UserBank::find()->where(['uid' => $this->uid])->andFilterWhere(['>', 'created_at', $start_time])->andFilterWhere(['<', 'created_at', $end_time])->count('id');
            if ($bind_count >= UserBank::BIND_COUNT) {
                $model->addError('account', '今日绑卡数已经超过' . UserBank::BIND_COUNT . '次，请明日重试');
            } else {
                if (empty($id)) {
                    $sn = $model->card_number . date('YmdH');
                    $sms = $model->sms;
                    $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx2532']);
                    $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
                    $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
                    $simpleXML->Body->TxSNBinding = $sn;
                    $simpleXML->Body->SMSValidationCode = $sms;
                    $xmlStr = $simpleXML->asXML();
                    $message = base64_encode(trim($xmlStr));
                    $payment = new \common\lib\cfca\Payment();
                    $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
                    $response = $payment->cfcatx_transfer($message, $signature);
                    $plainText = trim(base64_decode($response[0]));
                    $ok = $payment->cfcaverify($plainText, $response[1]);
                    $request = "";
                    $response_code = "";
                    if ($ok != 1) {
                        return $this->redirect('/user/draw/bindstatus?status=defeat');
                    } else {
                        $request = $plainText;
                        $simpleXML = new \SimpleXMLElement($plainText); //
                        $response_code = $simpleXML->Head->Code;
                        $VerifyStatus = $simpleXML->Body->VerifyStatus; //短信验证状态 20=验证码超时 30=验证未通过 40=验证通过 验证码超时或验证未通过状态，返回Status为空 短信验证状态为40=验证通过时才需要解析Status
                        $cfcalog = new CfcaLog();
                        $cfcalog->type = CfcaLog::TYPE_BINDCARD_RES;
                        $cfcalog->account_id = '';
                        $cfcalog->uid = $this->uid;
                        $cfcalog->log_type = 2;
                        $cfcalog->response = $plainText;
                        $cfcalog->save();
                        if ($VerifyStatus == '40') {
                            $res = $model->save();
                            //return $this->render('bindcardnew', ['model' => $model, 'bank_show' => $bank_show,"mobile"=>$mobile,'idcard'=>$idcard,'step'=>2]);
                            return $this->redirect('/user/draw/bindcardnew?id=' . $model->id);
                        } else {
                            return $this->redirect('/user/draw/bindstatus?status=defeat');
                        }
                    }
                } else {
                    $model->status=1;
                    $res = $model->save();
                    return $this->redirect('/user/draw/bindaudit');
                }
            }
        }
        return $this->render('bindcardnew', ['model' => $model, 'bank_show' => $bank_show, "mobile" => $mobile, 'idcard' => $idcard, 'step' => $id ? 2 : 1]);
    }

    public function actionBindcarddo() {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $start_time = strtotime(date('Y-m-d') . ' 0:00:00');
        $end_time = strtotime(date('Y-m-d') . ' 23:59:59');
        //$model = new UserBanks();
        $posts = Yii::$app->request->post()['UserBanks'];
        if (!empty($posts['card_number'])) {
            $ub = UserBank::find()->where(['card_number' => $posts['card_number'], 'uid' => $this->uid])->one();
            if ($ub && $ub->status == UserBank::STATUS_YES) {
                return ['res' => 0, 'msg' => '此卡已经绑定'];
            }
        } else {
            return ['res' => 0, 'msg' => '卡号不能为空'];
        }
        $mobile = $this->user->mobile;
        $idcard = ($this->user->type == 1) ? $this->user->idcard : $this->user->law_master_idcard;
        $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx2531']);
        $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML->Body->TxSNBinding = $posts['card_number'] . date('YmdH');
        $simpleXML->Body->BankID = $posts['bank_id'];
        $simpleXML->Body->AccountName = $posts['account'];
        $simpleXML->Body->AccountNumber = $posts['card_number'];
        $simpleXML->Body->IdentificationType = 1;
        $simpleXML->Body->IdentificationNumber = $idcard;
        $simpleXML->Body->PhoneNumber = $mobile;
        $simpleXML->Body->CardType = 10;
        $xmlStr = $simpleXML->asXML();

        $cfcalog = new CfcaLog();
        $cfcalog->type = CfcaLog::TYPE_BINDCARD;
        $cfcalog->account_id = '';
        $cfcalog->uid = $this->uid;
        $cfcalog->log_type = 1;
        $cfcalog->response = $xmlStr;
        $cfcalog->save();

        $message = base64_encode(trim($xmlStr));
        $payment = new \common\lib\cfca\Payment();
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = trim(base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
        $request = "";
        $response_code = "";
        if ($ok != 1) {
            $request = \Yii::$app->functions->createXmlResponse('2002', "验签失败");
            return ['res' => 0, 'msg' => '验签失败'];
        } else {
            $request = $plainText;
            $simpleXML = new \SimpleXMLElement($plainText); //
            $response_code = $simpleXML->Head->Code;
            $response_msg = $simpleXML->Head->Message;
            $cfcalog = new CfcaLog();
            $cfcalog->type = CfcaLog::TYPE_BINDCARD;
            $cfcalog->account_id = '';
            $cfcalog->uid = $this->uid;
            $cfcalog->log_type = 2;
            $cfcalog->response = $plainText;
            $cfcalog->save();

            if ($response_code == '2000') {
                return ['res' => 1, 'msg' => 'success'];
            } else {
                return ['res' => 0, 'msg' => '获取验证码失败'];
            }
        }
    }

    /* 0928 end */

    /* 0925 start */

    public function actionBcard() {
        $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx2531']);
        $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML->Body->TxSNBinding = time();
        $simpleXML->Body->BankID = 102;
        $simpleXML->Body->AccountName = '张宏雨';
        $simpleXML->Body->AccountNumber = '130728199001170074';
        $simpleXML->Body->IdentificationType = 1;
        $simpleXML->Body->IdentificationNumber = '6222020200060690696';
        $simpleXML->Body->PhoneNumber = '15810036547';
        $simpleXML->Body->CardType = 10;
        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $payment = new \common\lib\cfca\Payment();
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = trim(base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
        $request = "";
        $response_code = "";
        if ($ok != 1) {
            $request = \Yii::$app->functions->createXmlResponse('2002', "验签失败");
        } else {
            $request = $plainText;
            $simpleXML = new \SimpleXMLElement($plainText); //
            $response_code = $simpleXML->Head->Code;
        }
        echo "<pre>xmlstr:";
        print_r($xmlStr);
        echo "response_code<br/>";
        print_r($response_code);
        echo "request<br/>";
        print_r($request);
    }

    public function actionBcards($sn = null, $sms = null) {
        if (!empty($sms)) {
            $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx2532']);
            $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
            $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
            $simpleXML->Body->TxSNBinding = $sn;
            $simpleXML->Body->SMSValidationCode = $sms;
            $xmlStr = $simpleXML->asXML();
            $message = base64_encode(trim($xmlStr));
            $payment = new \common\lib\cfca\Payment();
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $response = $payment->cfcatx_transfer($message, $signature);
            $plainText = trim(base64_decode($response[0]));
            $ok = $payment->cfcaverify($plainText, $response[1]);
            $request = "";
            $response_code = "";
            if ($ok != 1) {
                $request = \Yii::$app->functions->createXmlResponse('2002', "验签失败");
            } else {
                $request = $plainText;
                $simpleXML = new \SimpleXMLElement($plainText); //
                $response_code = $simpleXML->Head->Code;
            }
            echo "<pre>xmlstr:";
            print_r($xmlStr);
            echo "response_code<br/>";
            print_r($response_code);
            echo "request<br/>";
            print_r($request);
        }
    }

    /* 0925 end */
}
