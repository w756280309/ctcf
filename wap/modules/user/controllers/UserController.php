<?php

namespace app\modules\user\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\user\User;
use common\core\UserAccountCore;
use common\models\user\MoneyRecord;
use common\service\OrderService;
use common\service\BankService;

class UserController extends BaseController {

    public function actionIndex(){
        $this->layout='account';
        $uacore = new UserAccountCore();
        $ua = $uacore->getUserAccount($this->uid);
        $leijishouyi = $uacore->getTotalProfit($this->uid);//累计收益
        $dhsbj = $uacore->getTotalWaitMoney($this->uid);//带回收本金
        $zcze = $uacore->getTotalFund($this->uid);//资产总额=理财资产+可用余额+冻结金额
        
        $data = BankService::checkKuaijie($this->uid);
        
        return $this->render('index',['ua'=>$ua,'user'=>$this->user,'ljsy'=>$leijishouyi,'dhsbj'=>$dhsbj,'zcze'=>$zcze, 'data' => $data]);
    }
    
    public function actionMingxi() {
        $this->layout = "@app/modules/order/views/layouts/buy";
        $model = MoneyRecord::find()->where(['uid' => $this->uid])->select('created_at,type,in_money,out_money,balance,status')->orderBy("id desc")->asArray()->all();
        $arr = array();
        $desc = array();
        foreach($model as $key => $val) {
            if($val['status'] == MoneyRecord::STATUS_SUCCESS) {
                $arr[$key] = $val;
            } else {          
                if($val['type'] == MoneyRecord::TYPE_RECHARGE || $val['type'] == MoneyRecord::TYPE_DRAW) {
                    if($val['status'] == MoneyRecord::STATUS_ZERO) {
                        $desc[$key] = '处理中';
                        $arr[$key] = $val;
                    } else if($val['status'] == MoneyRecord::STATUS_FAIL) {
                        $desc[$key] = '失败';
                        $arr[$key] = $val;
                    }
                }
                if($val['type'] == MoneyRecord::TYPE_ORDER && $val['status'] == MoneyRecord::STATUS_REFUND) {
                    $desc[$key] = '退款';
                    $arr[$key] = $val;
                }
            }
        }        
        return $this->render('mingxi',['model' => $arr, 'desc' => $desc]);
    }

    public function actionMyorder($type=null,$page=1){
        $this->layout = "@app/modules/user/views/layouts/myorder";
        $os = new OrderService();
        $list = $os->getUserOrderList($this->uid,$type,$page);
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $list;
        }
        return $this->render('order',['list'=>$list, 'type'=>$type]);
    }
    
    
    
}
