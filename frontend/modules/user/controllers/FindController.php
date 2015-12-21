<?php

namespace app\modules\user\controllers;

use Yii;
use common\models\user\User;
use yii\web\Controller;
use common\models\Sms;
use common\models\Rest;
use common\models\sms\SmsTable;
use common\models\Functions;

class FindController extends Controller {

    public function actionIndex($step = 1,$mobile = null) {
        $model=new User();
        $func = new Functions();
        $model->scenario ='find_pwd';//echo 1;exit;
        if(empty($mobile)&&$step==1){
            $model = new User();
            $model->scenario ='find_pwd';//echo 1;exit;
        }else{
            $mobile = $func->passport_decrypt($mobile,"");
            //var_dump(1);exit;
            $model = User::findOne(['mobile'=>$mobile]);
            if(is_null($model)){
                return $this->redirect('/user/find?step=1');
            }
            $model->scenario ='find_pwd_1';
        }
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            {
                if($step==1){
                    $bool_model = User::findOne(['mobile'=>$model->mobile]);
                    if(is_null($bool_model)){
                        $model->addError('mobile',"手机号尚未注册过");
                    }
                    $str = $func->passport_encrypt($model->mobile,"");
                    return $this->redirect('/user/find?step=2&mobile='.$str);
                }else{
                    $sms_model = new SmsTable();
                    $sms_res = "";
                    $sms_id = "";
                    $sms_re = $sms_model->verifyCode(array('uid'=>$model->id,'mobile'=>$model->mobile,'type'=>SmsTable::TYPE_FIND_PWD));
                    //var_dump(array('uid'=>$model->id,'mobile'=>$model->mobile,'type'=>SmsTable::TYPE_FIND_PWD),$sms_re);
                    if($sms_re['result']==0){
                        $sms_res=$sms_re['error'];
                    }else{
                        $sms_id = $sms_re['obj_id'];
                    }
                    if(empty($sms_res)){
                        $model->setPassword($model->f_pwd);
                        $model->save();
                        $sms_model_up=SmsTable::findOne(['id'=>$sms_id]);//
                        $sms_model_up->status=1;
                        $sms_model_up->save();
                        return $this->redirect('/'); 
                    }else{
                        $model->addError('sms_code',$sms_res);
                    }
                    
                }
            }
        }
        return $this->render('find', ['model' => $model,'step'=>$step,'mobile'=>$func->passport_encrypt($mobile,"")]);
    }
    

}
