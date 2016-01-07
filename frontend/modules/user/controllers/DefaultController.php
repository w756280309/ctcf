<?php

namespace app\modules\user\controllers;

use Yii;
use common\models\user\User;
use common\models\order\OfflineOrder;
use yii\web\Controller;
use yii\data\Pagination;
//use common\models\user\LoginForm;
use common\models\user\UserType;
//use common\models\user\ChangePwd;
use common\models\GuoZhengTong;
use common\models\Sms;
use common\models\Rest;
use common\models\sms\SmsTable;
use common\models\contract\Contract;
use common\lib\product\ProductProcessor;
use yii\web\Response;
use frontend\controllers\BaseController;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\user\DrawRecord;
use common\models\order\OnlineRepaymentRecord;

class DefaultController extends BaseController {

    public $layout = 'main';

    public function actionIndex() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/');
        } else {
            $model = \Yii::$app->user->identity;
            if (Yii::$app->user->getIdentity()->channel_id) {
                return $this->redirect('/user/default/means?current=2');
            }
            $cat = UserType::find()->where("id=" . $model->cat_id)->select('name')->one();
            return $this->render('index', ['model' => $model, 'user_cat' => (empty($cat->name) ? "" : $cat->name)]);
        }
    }

    public function actionConfirmemail($token = null) {
        if (empty($token) || (\Yii::$app->user->isGuest)) {
            return $this->redirect('/');
        }
        $common_path = Yii::getAlias('@common');
        $pub_key_path = $common_path . '/components/rsa/own/rsa_public_key.pem';
        $pub_decrypt = \Yii::$app->functions->rsaPubDecrypt($pub_key_path, $token);
        if (empty($pub_decrypt)) {
            return $this->redirect('/');
        }
        $code = $pub_decrypt;
        if (strpos($code, "%") === FALSE) {
            return $this->redirect('/');
        }
        $real_code = "";
        $time = 0;
        //简单混淆用户code
        if (!empty($code) && strpos($code, "C99") == 0 && strpos($code, "899") == 10) {
            $real_code = substr($code, 3, 7);
            $time = substr($code, strpos($code, "%") + 1, strlen($code));
        }
        $min = time() - $time;
        $hour = 1 * 60 * 60;
        if ($min > $hour) {
            return $this->redirect('/'); //超过一小时的不执行
        }
        $model = (User::findOne(['usercode' => $real_code]));
        if (is_null($model)) {
            return $this->redirect('/');
        }
        $model->scenario = 'examin_email';
        $model->email_status = User::EMAIL_STATUS_PASS;
        $model->save();
        return $this->redirect('/user/register/confirmemail');
    }

    public function actionEdit() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/');
        } else {
            $model = \Yii::$app->user->identity;
            $cat = UserType::find()->where("id=" . $model->cat_id)->select('name')->one();
            $category = array();
            $data = UserType::find()->where(['status' => UserType::STATUS_SHOW])->all();
            foreach ($data as $val) {
                $category[$val->id] = $val->name;
            }
            if ($model->type == 1) {
                $model->scenario = 'front_reg_2_1';
            } else if ($model->type == 2) {
                $model->scenario = 'front_reg_2_2';
            } else {
                return $this->redirect('/user');
            }
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $tong_model = new GuoZhengTong();
                $re = FALSE;
                if ($model->type == 1) {
                    $re = $tong_model->check($model->real_name, $model->idcard);
                } else if ($model->type == 2) {
                    $re = $tong_model->check($model->law_master, $model->law_master_idcard);
                }
                //$re=true;
                if ($re) {
                    $model->idcard_status = User::IDCARD_STATUS_PASS;
                }
                $model->save();
                return $this->redirect('/user');
            }
            return $this->render('edit', ['model' => $model, 'user_cat' => (empty($cat->name) ? "" : $cat->name), 'category' => $category]);
        }
    }

    //线下我的资产
//	public function actionMeans($tab = 0) {
//		if (Yii::$app->user->getIdentity()->channel_id) {
//			$condition = ['channel_user_sn' => Yii::$app->user->getIdentity()->channel_user_sn];
//			$data = ChannelOrder::find()->andWhere($condition);
//
//			$pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
//			$model = $data->asArray()->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();
//			return $this->render('meansdmxy', ['model' => $model, "pages" => $pages, 'tab' => $tab]);
//		} else {
//			$condition = array();
//			if (!empty($tab)) {
//				$condition = ['user_id' => \Yii::$app->user->id, 'type' => 1, 'deal_status' => 1];
//			} else {
//				$condition = ['user_id' => \Yii::$app->user->id, 'type' => 0];
//			}
//			$data = OfflineOrder::find()->select("id,product_sn,type,product_title,yield_rate,product_duration,order_time,pay_time,contract_sn,order_money")->andFilterWhere(['<', 'status', OfflineOrder::STATUS_DEL])->andWhere($condition);
//
//			$pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
//			$model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();
//
//			return $this->render('means', ['model' => $model, "pages" => $pages, 'tab' => $tab]);
//			//我的资产
//			//return $this->render('tender-myassets', ['model' => $model, "pages" => $pages, 'tab' => $tab]);
//			//我的融资
//			//return $this->render('tender-myfinancing', ['model' => $model, "pages" => $pages, 'tab' => $tab]);
//		}
//	}

    public function actionContract($order_id = null, $template = null, $op = null) {
        //echo 1;exit;
        set_time_limit(0);
//        if (\Yii::$app->user->isGuest) {
//            return $this->redirect('/');
//        }
        require_once "../../common/components/tcpdf/tcpdf.php";

        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $contract = Contract::findOne(['order_id' => $order_id, 'contract_template_id' => $template]);
        //echo $contract->contract_content;exit;
        //$pdf->SetFont('stsongstdlight', '', 5);
        $pdf->SetHeaderData('', '', $contract->contract_name, '');

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // add a page
        $pdf->AddPage();
        // set font
        //$pdf->SetFont('stsongstdlight', '', 5);
        $txt = $contract->contract_content;

        $pdf->MultiCell(0, '', $txt, 0, 'L', $fill = 0, $ln = 1, '', '', 0, true, true, 0);
        //MultiCell(宽, 高, 内容, 边框,文字对齐, 文字底色, 是否换行, x坐标, y坐标, 变高, 变宽, 是否支持html, 自动填充, 最大高度)

        $file_name = $contract->contract_number; //不知中文怎么支持
        $pdf->Output($file_name . ".pdf", $op); /* 默认是I：在浏览器中打开，D：下载，F：在服务器生成pdf */
    }

    public function actionPopup($op = null) {
        $this->layout = FALSE;

        $common_path = Yii::getAlias('@common');
        $pub_key_path = $common_path . '/components/rsa/own/rsa_public_key.pem';
        $pri_key_path = $common_path . '/components/rsa/own/rsa_private_key.pem';

        if (\Yii::$app->user->isGuest || empty($op)) {
            return $this->redirect('/');
        } else {
            $model = \Yii::$app->user->identity;
            $email = "";
            if ($model->type == 1) {
                $model->scenario = 'front_reg_2_1';
            } else if ($model->type == 2) {
                $model->scenario = 'front_reg_2_2';
            } else {
                return $this->redirect('/user');
            }
            if ($op == "mobile_bind") {
                $model->scenario = 'bind_mobile';
            } else if ($op == "mobile_edit") {
                $pri = Yii::$app->request->get('pri');
                if (empty($pri)) {
                    return $this->redirect('/user/default/popup?op=mobile_bind');
                }
                $pub_decrypt = \Yii::$app->functions->rsaPubDecrypt($pub_key_path, $pri);
                if (empty($pub_decrypt)) {
                    return $this->redirect('/user/default/popup?op=mobile_bind');
                }
                $model->scenario = 'edit_mobile';
            } else if ($op == "pwd_edit") {
                $model->scenario = 'edit_pwd';
            } else if ($op == "mobile_verify") {
                $model->scenario = 'mobile_verify';
            } else if ($op == "email_edit") {
                $model->scenario = 'ucenter_1';
            } else if ($op == "email") {
                $model->scenario = 'ucenter_2';
            }

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($op == "email_edit") {
                    $model->email_status = 0;
                    $model->save();
                    $email_arr = explode('@', $model->email);
                    $email = "http://mail." . $email_arr[1];

                    $current = time();
                    $str = \Yii::$app->functions->rsaPriEncrypt($pri_key_path, "C99" . $model->usercode . "899%" . $current, "njfae_");
                    $this->sendMail(['op' => 'confirm_email', 'rev_mail' => $model->email, 'subject' => '邮箱确认', 'text_body' => $str]);
                    return $this->render('popup', ['model' => $model, 'op' => 'email_examin', 'email' => $email]);
                } else if ($op == "email") {
                    $email_arr = explode('@', $model->email);
                    $email = "http://mail." . $email_arr[1];

                    $current = time();
                    $str = \Yii::$app->functions->rsaPriEncrypt($pri_key_path, "C99" . $model->usercode . "899%" . $current, "njfae_");
                    $this->sendMail(['op' => 'confirm_email', 'rev_mail' => $model->email, 'subject' => '邮箱确认', 'text_body' => $str]);
                    return $this->render('popup', ['model' => $model, 'op' => 'email_examin', 'email' => $email]);
                } else if ($op == "mobile_bind") {
                    $bool_pwd = $model->validatePassword($model->password);
                    //var_dump($bool_pwd);
                    if (!$bool_pwd) {
                        $model->addError('password', "旧密码错误");
                    }
                    $sms_model = new SmsTable();
                    $sms_res = "";
                    $sms_id = "";
                    $sms_re = $sms_model->verifyCode(array('uid' => Yii::$app->user->id, 'mobile' => $model->mobile, 'type' => SmsTable::TYPE_EDIT_BIND_MOBILE));
                    if ($sms_re['result'] == 0) {
                        $sms_res = $sms_re['error'];
                    } else {
                        $sms_id = $sms_re['obj_id'];
                    }
                    if (empty($sms_res)) {
                        $sms_model_up = SmsTable::findOne(['id' => $sms_id]); //
                        $sms_model_up->status = 1;
                        $sms_model_up->save();
                        $pri_encrypt = \Yii::$app->functions->rsaPriEncrypt($pri_key_path, $model->mobile);
                        return $this->redirect('/user/default/popup?op=mobile_edit&pri=' . urlencode($pri_encrypt));
                    } else {
                        $model->addError('sms_code', $sms_res);
                    }
                } else if ($op == "mobile_verify") {
                    $sms_model = new SmsTable();
                    $sms_res = "";
                    $sms_id = "";
                    $sms_re = $sms_model->verifyCode(array('uid' => Yii::$app->user->id, 'mobile' => $model->mobile, 'type' => SmsTable::TYPE_REG_CODE));
                    if ($sms_re['result'] == 0) {
                        $sms_res = $sms_re['error'];
                    } else {
                        $sms_id = $sms_re['obj_id'];
                    }
                    if (empty($sms_res)) {
                        $model->mobile_status = User::MOBILE_STATUS_PASS;
                        $model->save();

                        $sms_model_up = SmsTable::findOne(['id' => $sms_id]); //
                        $sms_model_up->status = 1;
                        $sms_model_up->save();
                        return $this->redirect('/user/default/popup?op=mobile_verify_over');
                    } else {
                        $model->addError('sms_code', $sms_res);
                    }
                } else if ($op == "mobile_edit") {
                    $find_new = User::findOne(['mobile' => $model->new_mobile]);
                    if (!is_null($find_new)) {
                        $model->addError('new_mobile', "手机号已经注册过");
                    } else {
                        $sms_model = new SmsTable();
                        $sms_res = "";
                        $sms_id = "";
                        $sms_re = $sms_model->verifyCode(array('uid' => Yii::$app->user->id, 'mobile' => $model->new_mobile, 'type' => SmsTable::TYPE_EDIT_BIND_MOBILE, 'code' => $model->sms_code));
                        //var_dump($sms_re);exit;
                        if ($sms_re['result'] == 0) {
                            $sms_res = $sms_re['error'];
                        } else {
                            $sms_id = $sms_re['obj_id'];
                        }
                        if (empty($sms_res)) {
                            $model->mobile = $model->new_mobile;
                            $model->mobile_status = User::MOBILE_STATUS_PASS;
                            $model->save();
                            $sms_model_up = SmsTable::findOne(['id' => $sms_id]); //
                            $sms_model_up->status = 1;
                            $sms_model_up->save();
                            return $this->redirect('/user/default/popup?op=mobile_edit_over');
                        } else {
                            $model->addError('sms_code', $sms_res);
                        }
                    }
                } else if ($op == "pwd_edit") {
                    $bool_pwd = $model->validatePassword($model->old_password);
                    if (!$bool_pwd) {
                        $model->addError('old_password', "原密码错误");
                    } else {
                        $model->setPassword($model->new_password);
                        $model->save();
                        return $this->redirect('/user/default/popup?op=pwd_edit_over');
                    }
                }
            }
            //var_dump($model->getErrors());
            return $this->render('popup', ['model' => $model, 'op' => $op, 'email' => $email]);
        }
    }

    /**
     * $method 1短信 2电话
     */
    public function actionCheckmobile($mobile = null, $uid = null, $method = null, $temp = null) {

        if (empty($uid)) {
            echo json_encode(array('code' => 0, 'msg' => '参数错误'));
            exit;
        }
        if (!in_array($temp, array(SmsTable::SMS_CODE_FIND_PWD, SmsTable::SMS_CODE_EDIT_BIND_MOBILE, SmsTable::SMS_CODE_REG))) {
            echo json_encode(array('code' => 0, 'msg' => '参数错误'));
            exit;
        }
//        $csrf = Yii::$app->request->post('_csrf');
//        if (empty($csrf)) {
//            echo json_encode(array('code' => 0, 'msg' => 'csrf非法的请求方式'));
//            exit;
//        }
        if (!Yii::$app->request->isAjax) {
            echo json_encode(array('code' => 0, 'msg' => '非法的请求方式'));
            exit;
        }
        if (empty($mobile)) {
            echo json_encode(array('code' => 0, 'msg' => '手机号不能为空'));
            exit;
        }
        $bool_model = User::findOne(['mobile' => $mobile]);
        if (empty($bool_model)) {
            echo json_encode(array('code' => 0, 'msg' => '手机号不存在'));
            exit;
        }

        $reDate = '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/';
        $re = preg_match($reDate, $mobile);
        if (!$re) {
            echo json_encode(array('code' => 0, 'msg' => '非法手机号'));
            exit;
        }

        $code = User::createRandomStr();
        $content_arr = array();
        $type = 1;
        if ($temp == SmsTable::SMS_CODE_FIND_PWD) {
            $content_arr = array($code);
            $type = SmsTable::TYPE_FIND_PWD;
        } else if ($temp == SmsTable::SMS_CODE_EDIT_BIND_MOBILE) {
            $content_arr = array($code, SmsTable::SMS_TIME_LEN_REG);
            $type = SmsTable::TYPE_EDIT_BIND_MOBILE;
        } else if ($temp == SmsTable::SMS_CODE_REG) {
            $content_arr = array($code, SmsTable::SMS_TIME_LEN_REG);
            $type = SmsTable::TYPE_REG_CODE;
        }

        if ($method == 1) {
            $sms = new Sms();
            $data = $sms->sendTemplateSMS($mobile, $content_arr, $temp);
            if ($data->statusCode == SmsTable::SMS_STATUS_SUCCESS) {
                $re = true;
            }
        } else if ($method == 2) {
            $rest = new Rest();
            $res = $rest->voiceVerify($code, 3, $mobile, '', '');
            if ($res->statusCode == SmsTable::SMS_STATUS_SUCCESS) {
                $re = true;
            }
        } else {
            echo json_encode(array('code' => 0, 'msg' => '无效方案'));
            exit;
        }
        $sms_model = new SmsTable();
        if ($re) {
            $sms_model->type = $type;
            $sms_model->uid = $uid;
            $sms_model->time_len = SmsTable::SMS_TIME_LEN_REG;
            $sms_model->code = $code;
            $sms_model->mobile = $mobile;
            $sms_model->end_time = time() + SmsTable::SMS_TIME_LEN_REG * 60;
            $sms_model->save();
        }
        echo json_encode(array('code' => 1, 'msg' => 1));
        exit;
    }

    public function sendMail($param = array()) {
        if ($param['op'] == 'confirm_email') {
            $mail = Yii::$app->mailer->compose('confirmemail', ['token' => $param['text_body']]);
            //$mail->setFrom('service@njfae.cn');
            $mail->setFrom('service@njfae.cn');
            $mail->setTo($param['rev_mail']);
            $mail->setSubject($param['subject']);
            //$mail->setTextBody($param['text_body']);
            //$mail->setHtmlBody($param['html_body']);    //普通邮件发送
            $mail->send();
        }
    }

    public function actionChecklogin() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['is_login' => (\Yii::$app->user->isGuest), 'u' => Yii::$app->user->id];
    }

    public function actionFundmanage() {
        $id = Yii::$app->user->getIdentity()->id;
        $params = Yii::$app->request->get();
        $type = $params['type'];
        $session = Yii::$app->session;
        $uatype = $session->get('useraccount');
        $account_id = UserAccount::find()->where(['uid' => $id, 'type' => $uatype])->one()->id;
        $selectquery = array('uid' => $id, 'account_id' => $account_id);
        $remaining = UserAccount::getUserAccount($this->user->id, $uatype)->available_balance;

        //若$investtype = investtype 为融资人的资金管理，否则为投资人的资金管理
        $investtype = investtype;
        if (is_numeric($type)) {
            $selectquery['type'] = $type;
        }
        $data = MoneyRecord::find()->where($selectquery);
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '15']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();
        return $this->render('tender-fundmanage', ['type' => $type, 'model' => $model, 'pages' => $pages, 'remaining' => $remaining, 'investtype' => $investtype]);
    }

}
