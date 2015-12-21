<?php

namespace app\modules\user\controllers;

use Yii;
use common\models\user\User;
use common\models\user\UserType;
use common\models\user\LoginForm;
use yii\web\Controller;
use common\models\Sms;
use common\models\Rest;
use common\models\sms\SmsTable;
use common\models\GuoZhengTong;
use yii\web\Session;
use common\models\user\UserAccount;

class RegisterController extends Controller {

    public function actionReg($step = 1, $type = null, $code = null) {

        error_reporting(E_ALL ^ E_NOTICE);
        if (empty($type) || !in_array($type, array(1, 2))) {
            return $this->redirect('/user/register/prereg');
        }
        if ($step > 1 && empty($code)) {
            return $this->redirect('/user/register/prereg');
        }
        $real_code = "";

        //简单混淆用户code
        if (!empty($code) && strpos($code, "C99") == 0 && strpos($code, "899") == 10) {
            $real_code = substr($code, 3, 7);
        }

        $model = empty($real_code) ? new User() : (User::findOne(['usercode' => $real_code]));
        if (is_null($model)) {
            return $this->redirect('/user/register/reg?step=1&type=' . $type);
        }

        //注册机构会员第二步加载会员分类
        $category = array();
        $temp_uid = "";
        $session = Yii::$app->session;
        $session->open();
        if (empty($_SESSION['tmp_uid'])) {
            $session->set('tmp_uid', time() . "_" . mt_rand(10000, 99999)); //用户发送短信记录用户临时uid
            $temp_uid = $_SESSION['tmp_uid'];
        } else {
            $temp_uid = $_SESSION['tmp_uid'];
        }
        if ($step == 1) {
            $model->scenario = 'front_reg_1';
        } else if ($step == 2 && $type == 2) {
            $data = UserType::find()->where(['status' => UserType::STATUS_SHOW])->all();
            foreach ($data as $val) {
                $category[$val->id] = $val->name;
            }
            $model->scenario = 'front_reg_2_2';
        } else if ($step == 2 && $type == 1) {
            $model->scenario = 'front_reg_2_1';
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $sms_res = "";
            $sms_id = "";
            $gzt_err = "";
            if ($step == 1) {
                if ($model->password) {
                    $model->setPassword($model->password);
                }
                $post_temp_uid = $_POST['temp_uid'];
                $sms_model = new SmsTable();
                $sms_re = $sms_model->verifyCode(array('temp_uid' => $post_temp_uid, 'mobile' => $model->mobile, 'type' => SmsTable::TYPE_REG_CODE, 'code' => $model->sms_code));
                //var_dump($sms_re);exit;
                if ($sms_re['result'] == 0) {
                    $sms_res = $sms_re['error'];
                } else {
                    $sms_id = $sms_re['obj_id'];
                }
                $model->usercode = User::createCode(($model->type == 2 ? 9 : $model->type));
                if ($type == 1) {
                    $model->cat_id = 1; //个人是交易用户
                }
            } else if ($step == 2 && $type == 1) {//个人第二步需要验证身份证
                //$model->idcard_status=User::IDCARD_STATUS_PASS;
                if ($model->idcard_examin_count == User::IDCARD_EXAMIN_COUNT) {
                    $gzt_err = "实名认证错误，请联系金交中心工作人员，联系电话025-8570-8888";
                } else {
                    $tong_model = new GuoZhengTong();
                    $tong_re = $tong_model->check($model->real_name, $model->idcard);
                    if (!$tong_re) {
                        $gzt_err = "实名认证错误,请重新输入";
                    } else {
                        $model->idcard_status = User::IDCARD_STATUS_PASS;
                    }
                }
            }
            //
            if (empty($sms_res) && empty($gzt_err)) {
                $model->save();

                /* 用户添加账户 begin */
                $uac = UserAccount::find()->where(['uid' => $model->id, 'type' => 1])->count();
                if (!$uac) {
                    $ua = new UserAccount();
                    $ua->uid = $model->id;
                    $ua->type = UserAccount::TYPE_BUY;
                    $ua->save();
                }
                /* 用户添加账户 end */

                $user_model = new LoginForm();
                $user_model->username = $model->username;
                $user_model->password = $model->password;
                $user_model->login();
                if ($sms_id) {
                    $sms_model_up = SmsTable::findOne(['id' => $sms_id]); //
                    $sms_model_up->status = 1;
                    $sms_model_up->save();
                }
                return $this->redirect('/user/register/reg?step=' . ($step + 1) . "&type=" . $type . "&code=" . ("C99" . $model->usercode . "899"));
            } else {
                if (!empty($sms_res)) {
                    $model->addError('sms_code', $sms_res);
                }
                if (!empty($gzt_err)) {
                    $model->scenario = 'id_check';
                    $model->idcard_examin_count+=1;
                    $model->save();
                    $model->addError('idcard', $gzt_err);
                }
            }
        }
        return $this->render('reg', ['model' => $model, "step" => $step, 'type' => $type, 'temp_uid' => $temp_uid, 'category' => $category, 'code' => $code]);
    }

    public function actionTestgzt(){
        echo 123;
            $tong_model = new GuoZhengTong();
            $tong_re = $tong_model->check('张宏雨', '130728199001170074');
            var_dump($tong_re);
    }

    /**
     * $method 1短信 2电话
     */
    public function actionCheckmobile($mobile = null, $temp_uid = null, $method = null) {
        $csrf = Yii::$app->request->post('_csrf');
        if (empty($csrf)) {
            echo json_encode(array('code' => 0, 'msg' => 'csrf非法的请求方式'));
            exit;
        }
        if (empty($temp_uid)) {
            echo json_encode(array('code' => 0, 'msg' => '参数错误'));
            exit;
        }
        if (empty($mobile)) {
            echo json_encode(array('code' => 0, 'msg' => '手机号不能为空'));
            exit;
        }
        if (!Yii::$app->request->isAjax) {
            echo json_encode(array('code' => 0, 'msg' => '非法的请求方式'));
            exit;
        }
        $reDate = '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/';
        $re = preg_match($reDate, $mobile);
        if (!$re) {
            echo json_encode(array('code' => 0, 'msg' => '非法手机号'));
            exit;
        }
        $res = User::find()->where(array('mobile' => $mobile))->count();

        if ($res == 0) {
            $re = false;
            $code = User::createRandomStr();
            if ($method == 1) {

                $sms = new Sms();
                $data = $sms->sendTemplateSMS($mobile, array($code, SmsTable::SMS_TIME_LEN_REG), SmsTable::SMS_CODE_REG);
                //var_dump($data,$data->statusCode,SmsTable::SMS_STATUS_SUCCESS);exit;
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
                $sms_model->type = SmsTable::TYPE_REG_CODE;
                $sms_model->temp_uid = $temp_uid;
                $sms_model->time_len = SmsTable::SMS_TIME_LEN_REG;
                $sms_model->code = $code;
                $sms_model->mobile = $mobile;
                $sms_model->status = 0;
                $sms_model->end_time = time() + SmsTable::SMS_TIME_LEN_REG * 60;
                $sms_model->save();
            }
            echo json_encode(array('code' => 1, 'msg' => 1));
            exit;
        } else {
            echo json_encode(array('code' => 0, 'msg' => '手机号已经注册过'));
            exit;
        }
    }

    
    /**
     * $method 1短信 2电话
     */
    public function actionCheckfindmobile($mobile = null, $uid = null, $method = null, $temp = null) {
        
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
    
    public function actionRegdo() {
        $model = new User();
        $type = $_POST['User']['type'];
        if ($type == 2) {
            $type = 9;
        }
        $model->usercode = User::createCode($type);
        $model->scenario = 'front_reg_1';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            
        }
        //var_dump($model,$model->getErrors());
    }

    public function actionPrereg() {
        return $this->render('regselect');
    }

    public function actionConfirmemail() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/');
        } else {
            $model = \Yii::$app->user->identity;
            return $this->render('confirmemail', ['model' => $model]);
        }
    }

    public function actionCreateua($id = null) {
        if (empty($id)) {
            return;
        }
        $ua = UserAccount::getUserAccount($id);
        if (empty($ua)) {
            $ua = new UserAccount();
            $ua->uid = $id;
            $ua->type = UserAccount::TYPE_BUY;
            $ua->save();
        }
    }

}
