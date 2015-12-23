<?php

namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\product\OnlineProduct;
use common\models\contract\ContractTemplate;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\core\UserAccountCore;
use common\core\OrderCore;

class OrderController extends BaseController {
//class OrderController extends Controller {
    //public $layout='main';

    /**
     * 认购页面
     * @param type $sn 标的编号
     * @return page
     */
    public function actionIndex($sn = null) {
        $this->layout = 'buy';
        $buystatus = OnlineProduct::checkOnlinePro($sn); //不等于100时候不能投资
        //$buystatus = OnlineProduct::checkOnlinePro($sn,  $this->uid,$balance);//不等于100时候不能投资  balace是用户余额
        //判断是否合规
        if ($buystatus == OnlineProduct::ERROR_SUCCESS) {
            $uacore = new UserAccountCore();
            $ua = $uacore->getUserAccount($this->uid);
            $deal = OnlineProduct::findOne(['sn' => $sn]);
            //var_dump($deal->contract);
            $param['order_balance'] = OnlineOrder::getOrderBalance($deal->id); //计算标的可投余额;
            $param['my_balance'] = $ua->available_balance; //用户账户余额;
            return $this->render('index', ['deal' => $deal, 'param' => $param]);
        } else {
            $msg = OnlineProduct::getErrorByCode($buystatus);
            exit($msg);
        }
    }

    /**
     * ajax 验证标的是否可投
     * @param type $sn标的编号
     * @return type
     */
    public function actionCheckorder($sn = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $buystatus = OnlineProduct::checkOnlinePro($sn); //不等于100时候不能投资
        //$buystatus = OnlineProduct::checkOnlinePro($sn,  $this->uid,$balance);//不等于100时候不能投资  balace是用户余额
        //判断是否可投资
        if ($buystatus != OnlineProduct::ERROR_SUCCESS) {
            $msg = OnlineProduct::getErrorByCode($buystatus);
            return ['code' => 1, 'message' => $msg];
        }
        return ['code' => 0, 'message' => '验证通过'];
    }

    /**
     * 购买标的
     * @param type $sn
     * @return type
     */
    public function actionDoorder($sn = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $money = \Yii::$app->request->post('money');
        $trade_pwd = \Yii::$app->request->post('trade_pwd');
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($sn,$money,$trade_pwd);
        if($ret['code']!=PayService::ERROR_SUCCESS){
            return $ret;
        }
        $ordercore = new OrderCore();
        return $ordercore->createOrder($sn, $money,  $this->uid);
    }
    
    public function actionOrdererror(){
        $this->layout = "@app/modules/order/views/layouts/buy";
        return $this->render('error');
    }
    
    public function actionAgreement($id=null,$key=0) {
        $this->layout = "@app/modules/order/views/layouts/buy";
        
        $model = ContractTemplate::find()->where(['pid' => $id])->select('pid,name,content')->asArray()->all();
        
        return $this->render('agreement',['model' => $model, 'key_f' => $key, 'content' => $model[$key]['content']]);
    }

}
