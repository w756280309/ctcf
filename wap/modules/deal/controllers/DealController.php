<?php

namespace app\modules\deal\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\data\Pagination;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\core\OrderAccountCore;

class DealController extends Controller {

    /**
     * 获取理财列表
     * @param type $page
     * @return type
     */
    public function actionIndex($page = 1) {
        $this->layout='test';
        $data = OnlineProduct::find()->where(['del_status'=>OnlineProduct::STATUS_USE,'online_status'=>OnlineProduct::STATUS_ONLINE])->select('id k,sn as num,title,yield_rate as yr,status,expires as qixian,money,start_date as start,finish_rate');
        $count = $data->count();
        $size = 5;
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);
        $deals = $data->offset(($page-1)*$size)->limit($pages->limit)->orderBy('sort asc,id desc')->asArray()->all();
        foreach($deals as $key => $val)
        {
            $dates = Yii::$app->functions->getDateDesc($val['start']);
            $deals[$key]['start'] = date("H:i",$val['start']);
            $deals[$key]['start_desc'] = $dates['desc'];
            $deals[$key]['finish_rate'] = number_format($val['finish_rate']*100,0);
            $deals[$key]['yr'] = $val['yr']?number_format($val['yr']*100,2):"0.00";
            $deals[$key]['statusval'] = Yii::$app->params['productonline'][$val['status']];
        }
        $tp = ceil($count/$size);
        $code = ($page>$tp)?1:0;
        
        $header = [
            'count' => intval($count),
            'size'  => $size,
            'tp'    => $tp,
            'cp'    =>  intval($page)
        ];
            
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $message = ($page>$tp)?'数据错误':'消息返回';
            return ['header'=>$header,'deals'=>$deals,'code'=>$code,'message'=>$message];
        }
        return $this->render('index',['deals'=>$deals,'header'=>$header]);
    }

    /**
     * 标的详情页面
     * @param type $sn
     * @return type
     */
    public function actionDetail($sn = null) {
        if(empty($sn)){
            throw new \yii\web\HttpException(500,'标的编号不能为空');
        }
        $deals = OnlineProduct::find()->where(['online_status'=>OnlineProduct::STATUS_ONLINE,'del_status'=>OnlineProduct::STATUS_USE,'sn'=>$sn])->asArray()->one();
        if(empty($deals)){
            throw new \yii\web\HttpException(404,'此页面找不到');
        }
        $orderbalance=0;
        if($deals['status']>=OnlineProduct::STATUS_NOW){//募集期的取剩余
            $oac = new OrderAccountCore();
            $orderbalance = $oac->getOrderBalance($deals['id']);//项目可投余额
        }else{
            $orderbalance = $deals['money'];
        }
        $deals['deal_balace'] = $orderbalance;
        if($deals['status']==OnlineProduct::STATUS_PRE) {
            $start = Yii::$app->functions->getDateDesc($deals['start_date']);
            $deals['start_date'] = $start['desc'].date('H:i',$start['time']);
        }
        $f = $deals['yield_rate'];
        if(bccomp($f, 0.01)<0){
            $f=0.99;
        }
        $deals['yield_rate'] = $f;
        $this->layout = "@app/views/layouts/dealdeail";
        return $this->render('detail',['deal'=>$deals]);
    }
    
    /**
     * 获取投资记录
     * @param type $pid
     * @return type
     */
    public function actionOrderlist($pid = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(empty($pid)){
            return ['orders'=>[],'code'=>1,'message'=>"pid参数不能为空"];
        }
        $data = OnlineOrder::getOrderListByCond(['online_pid'=>$pid], 'mobile,order_time time,order_money money');
        foreach ($data as $key=>$dat){
            $data[$key]['mobile'] = substr_replace($dat['mobile'],'****',3,4);
            $data[$key]['time'] = date("Y-m-d",$dat['time']);
            $data[$key]['his'] = date("H:i:s",$dat['time']);
        }
        return ['orders'=>$data,'code'=>0,'message'=>"消息返回"];
    }
    
    /**
     * 立即认购
     * @param type $sn 标的sn
     */
    public function actionToorder($sn = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->toCart($sn);
        return $ret;
    }

    
}
