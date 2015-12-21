<?php

namespace app\modules\product\controllers;

use yii\web\Controller;
use common\models\product\OfflineProduct;
use common\models\product\ProductCategory;
use common\models\product\ProductField;
use frontend\models\ProductCategoryData;
use common\models\order\OfflineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use yii\data\Pagination;
use Yii;

class DefaultController extends Controller {


    public function actionIndex($cid = null) {
        error_reporting(E_ALL^E_NOTICE);
        $pc_table = ProductCategory::tableName();
        $p_table = OfflineProduct::tableName();
        $o = OnlineProduct::tableName();
        
        $cat_model = ProductCategory::findOne($cid);
        //$condition = [$p_table.'.del_status'=>  OfflineProduct::DEL_STATUS_SHOW,$p_table.".status"=>  OfflineProduct::STATUS_ACTIVE];
        $cat_condtion = array();
        $cids = array();
        if(empty($cid)){
            $cat_model = new ProductCategory();
            $cat_model->name="所有分类";
        }else{
            $cat_condtion = ['parent_id'=>$cid];
            $cids[]=$cid;
        }
        $pcd = ProductCategory::find()->select("id")->andWhere($cat_condtion)->all();
        
        foreach ($pcd as $v){
            $cids[]=$v['id'];
        }
        $db = Yii::$app->db;
        
        $common_field = "id,title,sn,money,yield_rate,start_money,del_status,created_at,";
        $online_field = "0 AS line,0 AS special_type,expires product_duration,expires_show product_duration_type,STATUS product_status,'' special_type_title,cid category_id,1 active,target,CASE STATUS WHEN 7 THEN 2 ELSE STATUS END AS torder";
        $offline_field = "1 AS line,special_type,product_duration,product_duration_type,product_status,special_type_title,category_id,status as active,0 target,7 as torder";
        
        $sql = 'select T.*,cat.code as cat_code from (SELECT '.$common_field.$online_field.' FROM '.$o.
                ' where target=0 and del_status=0 union all select '.$common_field.$offline_field.' from '.$p_table.' where status=1 and del_status=0)T inner join '.$pc_table.
                ' cat on cat.id=T.category_id where category_id in ('.  implode(',', $cids).') order by torder asc,created_at desc';
        $query = $db->createCommand($sql)->query();        
   //echo ($query->getRawSql());exit;
//        
//        $data = OfflineProduct::find()->select($p_table.".*,".$pc_table.".code as cat_code")
//                ->join("inner join ", $pc_table,$pc_table.'.id='.$p_table.'.category_id')
//                ->andWhere($condition)->andOnCondition("category_id in (".  implode(',', $cids).")");
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $db->createCommand($sql." limit ".$pages->offset.",".$pages->limit)->queryAll();    
        //$model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();
        //echo ($sql." limit ".$pages->offset.",".$pages->limit);
        return $this->render('index',['cat_model'=>$cat_model,'model'=>$model,"pages"=>$pages]);
    }

    public function actionDetail($id=null) {
        error_reporting(E_ALL^E_NOTICE);
        $model = OfflineProduct::findOne(['id'=>$id,'status'=>  OfflineProduct::STATUS_ACTIVE,'del_status'=>  OfflineProduct::DEL_STATUS_SHOW]);
        $cat_model = ProductCategory::findOne($model->category_id);
        $duration_type_list = OfflineProduct::durationTypeList();
        //var_dump($duration_type_list,$model->product_duration_type,$duration_type_list[$model->product_duration_type]);
        $allow = FALSE;//设置拒绝访问备查文件
        if(\Yii::$app->user->id){
            $user_model = \Yii::$app->user->getIdentity();
            //var_dump($user_model->type);
            if($user_model->type==2&&$user_model->examin_status==\common\models\user\User::EXAMIN_STATUS_PASS){//机构必须审核通过
                $allow=true;
            }else{
                $order = OfflineOrder::find()->where(['user_id'=>$user_model->id,'product_sn'=>$model['sn']])->count();
                if($order||$user_model->idcard_status==\common\models\user\User::IDCARD_STATUS_PASS){
                    $allow=TRUE;//实名认证或持有产品才可以看
                }
            }
        }
        $pro_field_model = ProductField::findAll(['product_id' => $id]);
        $field_arr = array();
        foreach ($pro_field_model as $key=>$attribute){
            $field_arr[$attribute->field_type][$key]['name']=$attribute->name;
            $field_arr[$attribute->field_type][$key]['content']=$attribute->content;
        }
        return $this->render('detail',['model'=>$model,"cat_model"=>$cat_model,'field'=>$field_arr,'allow'=>$allow,'duration_type'=>$duration_type_list[$model->product_duration_type]]);
    }
    
    public function actionSpecialdetail($id=null){
        error_reporting(E_ALL^E_NOTICE);
        $model = OfflineProduct::findOne(['id'=>$id,'status'=>  OfflineProduct::STATUS_ACTIVE,'del_status'=>  OfflineProduct::DEL_STATUS_SHOW]);
        $cat_model = ProductCategory::findOne($model->category_id);
        $p_status = $model->product_status;
        $model->product_status = OfflineProduct::getProductStatusAll($model->product_status);
        $model->special_type = OfflineProduct::getSpecialType($model->special_type);
        $order = OfflineOrder::find()->where(['type'=>1,'deposit_status'=>1,'product_sn'=>$model->sn])->max('order_money');
        if(empty($order)){
            $order='暂无报价';
        }else{
            $pcd = new ProductCategoryData();
            $order=$pcd->toFormatMoney($order);
        }
        return $this->render('specialdetail',['model'=>$model,"cat_model"=>$cat_model,'p_status'=>$p_status,'max_price'=>$order]);
    }
    
    public function actionQuote($pid = null,$next=null){
        $this->layout=FALSE;
        $model = OfflineProduct::findOne(['id'=>$pid,'status'=>  OfflineProduct::STATUS_ACTIVE,'del_status'=>  OfflineProduct::DEL_STATUS_SHOW]);
        if(empty($model)){
            echo ("无效的标");exit;
        }
        if($model->special_type==0){
            echo ("无效的特殊资产");exit;
        }
        
        $order = new OfflineOrder();
        $order->scenario = 'create_quote';
        if ($order->load(\Yii::$app->request->post()) && $order->validate()) {
            if(\Yii::$app->user->isGuest){
                $order->addError('order_money', '请登录');
            }else if($model->end_time <  time()){
                $order->addError('order_money', '报价结束');
            }else if($order->order_money<$model->money){
                $pcd = new ProductCategoryData();
                $zhuan_price=$pcd->toFormatMoney($model->money);
                $order->addError('order_money', '输入报价不能低于'.$zhuan_price);
            }else{
                $user =\Yii::$app->user->identity;
                //'user_id', 'product_sn', 'product_title', 'real_name', 'idcard', 'order_money', 'order_time'
                if(($user->type==1&&$user->idcard_status==User::IDCARD_STATUS_PASS)||($user->type==2&&$user->examin_status==User::EXAMIN_STATUS_PASS)){
                    $order->type=1;//特殊资产标记
                    $order->user_id=$user->id;
                    $order->username=$user->username;
                    $order->product_sn=$model->sn;
                    $order->product_title=$model->title;
                    $order->real_name=$user->real_name;
                    if($user->type==1){
                        $order->idcard=$user->idcard;
                    }else{
                        $order->idcard=$user->law_master_idcard;
                    }
                    $order->order_time=time();
                    $order->save();
                    return $this->redirect('/product/default/quote?pid='.$pid.'&next=1');
                }else{
                    $order->addError('order_money', '用户未通过审核');
                }
            }
        }
        
        return $this->render('quote',['model'=>$model,'order'=>$order,"next"=>$next]);
    }
    
    /*线下资产*/
    public function actionDatalist(){
       $condition = ['del_status'=>  OfflineProduct::DEL_STATUS_SHOW,"status"=>  OfflineProduct::STATUS_ACTIVE,'home_status'=>  OfflineProduct::HOME_STATUS_SHOW];
       $cat_ids = [4,5,6,7,8,9,10,11,13];//省去对分类的读取
       $result = array();
       $pcd = new ProductCategoryData();
       $duration_type_list = OfflineProduct::durationTypeList();
       foreach ($cat_ids as $v){
           $condition['category_id']=$v;
           //var_dump($condition);
           $pro_data = OfflineProduct::find()->select('id,title,sn,yield_rate,product_duration_type,money,start_money,product_duration,product_status,special_type,end_time,special_type_title')
                   ->andWhere($condition)->limit(4)->all(); 
           $data = array();
           foreach ($pro_data as $key=>$val){
               $data[$key]['id']=$val->id;
               $data[$key]['title']=$val->title;
               $data[$key]['short_title']=\Yii::$app->functions->cut_str($val->title,9);
               $data[$key]['money']=$pcd->toFormatMoney($val->money);
               //$data[$key]['special_type']=OfflineProduct::getSpecialType($val->special_type);
               $data[$key]['special_type']=$val->special_type_title;
               $data[$key]['end_time']=date("Y.m.d",$val->end_time);
               if(intval($val->start_money)){
                   $data[$key]['start_money']=$pcd->toFormatMoney($val->start_money);
               }else{
                   $data[$key]['start_money']="详见协议";
               }
               if($val->product_duration){
                   $data[$key]['product_duration']=$val->product_duration.$duration_type_list[$val->product_duration_type];
               }else{
                   $data[$key]['product_duration']="详见协议";
               }
               $data[$key]["product_status_title"]=  OfflineProduct::getProductStatusAll($val->product_status);
               $data[$key]['sn']=$val->sn;
               if(number_format($val->yield_rate,2)!='0.00'){
                   $data[$key]['yield_rate']=number_format($val->yield_rate,2)."%";
               }else{
                   $data[$key]['yield_rate']="详见协议";
               }
           }
           $result[$v]=$data;
       }//echo "<pre>";print_r($result);exit;
       return json_encode($result);
    }
    
    /*线下线上资产*/
    public function actionDatalistall(){
        $pc_table = ProductCategory::tableName();
        $p_table = OfflineProduct::tableName();
        $o = OnlineProduct::tableName();
        $db = Yii::$app->db;
        $common_field = "id,title,sn,money,yield_rate,start_money,del_status,created_at,";
        $online_field = "0 AS line,0 AS special_type,expires product_duration,expires_show product_duration_type,STATUS product_status,'' special_type_title,cid category_id,1 active,1 home_status,'' end_time ";
        $offline_field = "1 AS line,special_type,product_duration,product_duration_type,product_status,special_type_title,category_id,status as active,home_status,end_time";
        
        $sql = 'select * from (SELECT '.$common_field.$online_field.' FROM '.$o.
                ' where del_status=0 AND target=0 union all select '.$common_field.$offline_field.' from '.$p_table.' where del_status=0 AND status=1 and home_status='.OfflineProduct::HOME_STATUS_SHOW.')T '.
                ' where category_id=:category_id order by created_at desc limit 0,4';
        
       $cat_ids = [4,5,6,7,8,9,10,11,13,14];//省去对分类的读取
       $result = array();
       $pcd = new ProductCategoryData();
       $duration_type_list = OfflineProduct::durationTypeList();
       foreach ($cat_ids as $v){
           $pro_data = $db->createCommand($sql)->bindValue(':category_id', $v)->queryAll();
           $data = array();
           foreach ($pro_data as $key=>$val){
               $data[$key]['id']=$val['id'];
               $data[$key]['line']=($val['line']=='0')?"tender":"default";
               $data[$key]['title']=$val['title'];
               $data[$key]['short_title']=\Yii::$app->functions->cut_str($val['title'],9);
               $data[$key]['money']=$pcd->toFormatMoney($val['money']);
               //$data[$key]['special_type']=OfflineProduct::getSpecialType($val['special_type']);
               $data[$key]['special_type']=$val['special_type_title'];
               $data[$key]['end_time']='';
               if(!empty($val['special_type_title'])){
                   $data[$key]['end_time']=date("Y.m.d",$val['end_time']);
               }
               if(intval($val['start_money'])){
                   $data[$key]['start_money']=$pcd->toFormatMoney($val['start_money']);
               }else{
                   $data[$key]['start_money']="详见协议";
               }
               if($val['line']=='1'){
                    if($val['product_duration']){
                        $data[$key]['product_duration']=$val['product_duration'].$duration_type_list[$val['product_duration_type']];
                    }else{
                        $data[$key]['product_duration']="详见协议";
                    }
                    $data[$key]["product_status_title"]=  OfflineProduct::getProductStatusAll($val['product_status']);
                    if(number_format($val['yield_rate'],2)!='0.00'){
                        $data[$key]['yield_rate']=number_format($val['yield_rate'],2)."%";
                    }else{
                        $data[$key]['yield_rate']="详见协议";
                    }
               }else{
                   $data[$key]['product_duration']=$val['product_duration_type'];
                   $data[$key]['yield_rate']=number_format($val['yield_rate']*100,2)."%";
                   $data[$key]["product_status_title"]= OnlineProduct::getProductStatusAll($val['product_status']);
               }
               
               $data[$key]['sn']=$val['sn'];
               
           }
           $result[$v]=$data;
       }//echo "<pre>";print_r($result);exit;
       return json_encode($result);
    }
    
    public function actionDownload($file_dir='../../backend/web/upload/product/',$file_name=null){
        if (!file_exists($file_dir . $file_name)) { //检查文件是否存在
            echo "文件找不到";exit; 
        } else {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$file_name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_dir . $file_name));
            readfile($file_dir . $file_name);
        } 
    }
	
	public function actionAbc(){
		$abc = file_get_contents('weicontract.html');
		\Yii::$app->functions->createHetong($header = "123", $abc, $file = "123",$op = "I");
	}

    
}
