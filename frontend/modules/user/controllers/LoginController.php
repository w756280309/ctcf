<?php

namespace app\modules\user\controllers;

use Yii;
use yii\web\Controller;
use common\models\user\LoginForm;
use common\models\adv\AdvPos;
use common\models\adv\Adv;
use common\models\user\User;
use common\models\user\UserAccount;
use yii\web\Response;

class LoginController extends Controller {

    public $layout = 'login';

    public function actionIndex() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $adv = new Adv();
        $login_arr = $adv->getPosAdv(AdvPos::POS_LOGIN_LEFT);

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/');
        } else {
            return $this->render('index', [
                        'model' => $model,
                        'login_adv_arr' => $login_arr
            ]);
        }
    }

    /**
     * 弹出框登录
     */
    public function actionPopupLogin($op = NULL) {
        $this->layout = FALSE;
        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->login()) {
            return $this->redirect('/user/login/popup-account');//俩种账户都有的选择账户
            
            $login_user = \Yii::$app->user->identity;
            $uatype = UserAccount::selectAccount($login_user);
            $session = Yii::$app->session;
            if($uatype==3){
                return $this->redirect('/user/login/popup-account');//俩种账户都有的选择账户
            }else{
                $session->set('useraccount', $uatype);
                return $this->redirect('/user/login/popup-login?op=refresh');//只有一种账户的，调到相对应账户
            }
            
        }
        return $this->render('popup_login', [
                    'model' => $model,
                    'op' => $op
        ]);
    }

    /* 选择账户 */
    public function actionPopupAccount($r = NULL) {
        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $this->layout = FALSE;
        if(!empty($r)){
            $r = in_array($r, ['buy','raise'])?$r:"buy";
            $uaccount = ($r=='buy')?1:2;
            $session = Yii::$app->session;
            $session->set('useraccount', $uaccount);
            return $this->redirect('/user/login/popup-login?op=refresh');//选择对应账户
        }
        return $this->render('popup_account',['r' => $r]);
    }

    public function actionSelectAccount(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        if (\Yii::$app->user->isGuest) {
            return ['res'=>0,'url'=>"/user/login/popup-login"];
        }else{
            $session = Yii::$app->session;
            if($session->get('useraccount')){
               return ['res'=>1]; 
            }else{
                $login_user = \Yii::$app->user->identity;
                $uatype = UserAccount::selectAccount($login_user);
                $session = Yii::$app->session;
                if($uatype==3){
                    //return $this->redirect('/user/login/popup-account');//俩种账户都有的选择账户
                    return ['res'=>0,'url'=>"/user/login/popup-account"];
                }else{
                    $session->set('useraccount', $uatype);
                    return ['res'=>1]; 
                }
            }
        }
    }
    
    /**
     * 渠道登录
     * @return type
     */
    public function actionChannelLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $adv = new Adv();
        $login_arr = $adv->getPosAdv(AdvPos::POS_LOGIN_LEFT);

        $model = new LoginForm();
        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $user = User::findOne(['username' => $model->username]);

            if (empty($user)) {
                $model->addError('password', '没有此用户');
            } else if ($user->channel_id == 0) {
                $model->addError('password', '金多多没有此用户');
            } else if ($user->init_pwd == $model->password) {
                $model->login();
                return $this->redirect('/user/default/means?current=2');
            } else {
                $model->addError('password', '未知明错误');
            }
        } else {
            return $this->render('clogin', [
                        'model' => $model,
                        'login_adv_arr' => $login_arr
            ]);
        }
    }

    public function actionAlog() {
        return json_encode(array("res" => 1));
    }

    public function actionLogout() {
        if (\Yii::$app->user->isGuest) {
            return $this->goHome();
        } else {
            \Yii::$app->user->logout();
            return $this->redirect('/');
        }
    }

}
