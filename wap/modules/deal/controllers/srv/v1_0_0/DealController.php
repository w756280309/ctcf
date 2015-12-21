<?php
namespace app\modules\deal\controllers\srv\v1_0_0;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\data\Pagination;
use app\modules\deal\core\Deal;

class DealController extends Controller {

    //public $layout='main';
    public function actionIndex($cid = null,$nid=null) {
//        $this->layout='test';
//        return $this->render('index');
        $deal = new Deal();
        $deal->getDealsCountByCond();
    }
    
    /**
     * 标的详情页面
     * @param type $sn
     * @return type
     */
    public function actionDetail($sn = null) {
        if(empty($sn)){
            echo "标的编号不能为空";exit;
        }
        $deals = OnlineProduct::find()->where(['online_status'=>0,'sn'=>$sn])->asArray()->one();
        $deals['deal_balace'] = "1000";//项目可投余额
        //var_dump($deals);
        $this->layout = "@app/views/layouts/dealdeail";
        return $this->render('detail',['deal'=>$deals]);
    }
    

    /**
     * 理财产品列表页面
     * @param type $page 1
     */
    public function actionDealresult($page=1){
        Yii::$app->response->format = Response::FORMAT_JSON;
//        $deals = [
//            ['num'=>'1000001',"title"=>"金服通1号","yr"=>"10.50%","status"=>"预告期",'qixian'=>200,'fp'=>0,'start'=>'2015-11-12 15:00:00'],
//            ['num'=>'1000002',"title"=>"金服通N12000号","yr"=>"8.50%","status"=>"募集期",'qixian'=>90,'fp'=>50,'start'=>'2015-11-12 15:00:00'],
//            ['num'=>'1000003',"title"=>"金服通JFT12000号","yr"=>"9.50%","status"=>"满标",'qixian'=>100,'fp'=>100,'start'=>'2015-11-12 15:00:00'],
//            ['num'=>'1000004',"title"=>"金服通JFT12000号","yr"=>"9.50%","status"=>"还款中",'qixian'=>100,'fp'=>100,'start'=>'2015-11-12 15:00:00'],
//            ['num'=>'1000005',"title"=>"金服通JFT12000号","yr"=>"9.50%","status"=>"已还款",'qixian'=>100,'fp'=>100,'start'=>'2015-11-12 15:00:00'],
//        ];
        $data = OnlineProduct::find()->where(['del_status'=>0,'online_status'=>1])->select('sn as num,title,yield_rate as yr,status,expires as qixian,money,start_date as start');
        $total = $data->count();
        $pages = new Pagination(['totalCount' => $total, 'pageSize' => '5']);
        $deals = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->asArray()->all();
        foreach($deals as $key => $val)
        {
            if (!empty($val['start']))
                $deals[$key]['start'] = strtotime ('Y-m-d H:i:s', $val['start']);
            $deals[$key]['yr'] = $val['yr']?number_format($val['yr']*100,2):"0.00";
            $deals[$key]['status'] = Yii::$app->params['productonline']['status'][$val['status']];
            $deals[$key]['fp'] = 100;
        }
        $header = [
            'page'=>$pages->offset,
            'total'=>$total,
            'size'=>5
        ];
        return ['header'=>$header,'deals'=>$deals,'code'=>0,'message'=>"消息返回"];
    } 

    
    public function actionOrderlist($pid = null){
        Yii::$app->response->format = Response::FORMAT_JSON;
        if(empty($pid)){
            return ['orders'=>[],'code'=>1,'message'=>"pid参数不能为空"];
        }
//        $data = OnlineOrder::getOrderListByCond(['online_pid'=>$pid], 'mobile,order_time time,order_money money');
//        foreach ($data as $key=>$dat){
//            $data[$key]['mobile'] = substr_replace($dat['mobile'],'****',3,4);
//            $data[$key]['time'] = date("Y-m-d H:i:s",$dat['time']);
//        }
        $data = [
            ['mobile'=>'158****0001',"time"=>"2015-11-12 10:23:23","money"=>"50000"],
            ['mobile'=>'158****0002',"time"=>"2015-11-12 10:23:23","money"=>"50000"],
            ['mobile'=>'158****0003',"time"=>"2015-11-12 10:23:23","money"=>"50000"],
            ['mobile'=>'158****0004',"time"=>"2015-11-12 10:23:23","money"=>"50000"],
            ['mobile'=>'158****0005',"time"=>"2015-11-12 10:23:23","money"=>"50000"],
        ];
        return ['orders'=>$data,'code'=>0,'message'=>"消息返回"];
    }
    
}
