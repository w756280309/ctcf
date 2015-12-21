<?php

namespace app\modules\user\controllers;

use Yii;
use common\models\user\User;
use yii\web\Controller;
use yii\data\Pagination;
use common\models\contract\Contract;
use common\lib\product\ProductProcessor;
use yii\web\Response;
use frontend\controllers\BaseController;
use common\models\user\UserAccount;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentRecord;

class OnlinetenderController extends BaseController {

    public $layout = 'main';

    public function actionMeans($tab = 0,$status=2) {
        $session = Yii::$app->session;
        $meanstype = $session->get('useraccount');
        if ($meanstype == 2) {//其实等于2融资  
            return $this->myFinancing($status);
        } else {
            return $this->myAssets($status);
        }
    }

    /* 融资人账户 */
    public function myFinancing($status=2) {
        $status = empty($status) ? 2 : $status;
        if ($status == 2) {
            $status = [OnlineProduct::STATUS_FOUND, OnlineProduct::STATUS_NOW];
        }
        $record = OnlineProduct::find()->where(['borrow_uid'=>($this->uid),'status'=>$status]);
        $count = $record->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => '20']);
        $model = $record->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->asArray()->all();
        $pp = new ProductProcessor();
        foreach($model as $key=>$val){
            $model[$key]['refund_time']=$pp->LoanTerms('d1', date("Y-m-d", $val['end_date']), $val['expires']);
        }
        return $this->render('myfinancing', ['model' => $model, "pages" => $pages, "tab" => '0', 'status' => $status, 'count' => $count]);
    }

    /* 投资人账户 */
    public function myAssets($status=2) {
        $params = Yii::$app->request->get();
        $id = $this->uid;
        $db = Yii::$app->db;
        $product_table = OnlineProduct::tableName();
        $order_table = OnlineOrder::tableName();
        $repay_table = OnlineRepaymentRecord::tableName();
        $status = empty($params['status']) ? 2 : $params['status'];
        if ($status == 5) {
            $status = [OnlineProduct::STATUS_FOUND, OnlineProduct::STATUS_HUAN];
        }
        $selectmeans = array($order_table . ".uid" => $id, $product_table . '.status' => $status);
        $record = OnlineOrder::find()->innerJoin($product_table, $order_table . ".online_pid=" . $product_table . ".id")
                ->leftJoin($repay_table, $repay_table . ".order_id=" . $order_table . '.id')
                ->select($order_table . ".*," . $product_table . '.title,' . $product_table . '.money,' . $product_table . '.yield_rate,' . $product_table .
                        '.refund_method,' . $product_table . '.expires,' . $product_table . '.start_date,' . $product_table . '.end_date,' .$product_table . '.status pstatus,' .
                        $repay_table . '.lixi,' . $repay_table . '.refund_time')
                ->where($selectmeans);

        $count = $record->count();
        $pages = new Pagination(['totalCount' => $record->count(), 'pageSize' => '20']);
        $record = $record->offset($pages->offset)->limit($pages->limit)->orderBy('id desc');
        $sql = $record->createCommand()->getRawSql(); //echo $sql;exit;
        $model = $db->createCommand($sql)->queryAll();
        $pp = new ProductProcessor();
        foreach ($model as $key => $val) {
            if (empty($val['lixi'])) {
                $pr = $pp->getProductReturn($val);
                $model[$key]['lixi'] = $pr['order_return'];
            }
            if (empty($val['refund_time'])) {
                $model[$key]['refund_time'] = $pp->LoanTerms('d1', date("Y-m-d", $val['order_time']), $val['expires']);
            } else {
                $model[$key]['refund_time'] = date("Y-m-d", $val['refund_time']);
            }
        }
        return $this->render('myassets', ['model' => $model, "pages" => $pages, "tab" => '0', 'status' => $status, 'count' => $count]);
    }

    /**
     * 合同列表
     */
    public function actionContractList($data = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (\Yii::$app->user->isGuest) {
            return ['res' => 0, 'data' => "", 'msg' => '合同读取失败'];
        } 
        else if (!Yii::$app->request->isAjax) {
            return ['res' => 0, 'data' => "", 'msg' => '无法读取'];
        } 
        else if (empty($data)) {
            return ['res' => 0, 'data' => "", 'msg' => '无法获取参数'];
        } else {
            $idarr = explode(',', $data);
            $list = Contract::find()->where(['type' => [2,3], 'order_id' => $idarr])->select('contract_name,id,path,order_sn,contract_content')->asArray()->all();
            $result = array();
            foreach ($list as $key => $val) {
                $result[$val['order_sn']][$key] = $val;
            }
            return ['res' => 1, 'data' => $result, 'msg' => 'success'];
        }
    }

    /**
     * 获取产品详情
     * @param type $data
     * @return type
     */
    public function actionProInfo($pid = null,$status = null) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (\Yii::$app->user->isGuest) {
            return ['res' => 0, 'data' => "", 'msg' => '合同读取失败'];
        } 
//        else if (!Yii::$app->request->isAjax) {
//            return ['res' => 0, 'data' => "", 'msg' => '无法读取'];
//        } 
        else if (empty($pid)) {
            return ['res' => 0, 'data' => "", 'msg' => '无法获取参数'];
        } else {
            $pp = new \common\lib\product\ProductProcessor();
            $idarr = explode(',', $pid);
            $list = OnlineProduct::find()->where(['id' => $idarr])->select('expires,id,status,id,refund_method,start_date,end_date,full_time')->asArray()->all();
            foreach ($list as $key => $val) {
                $list[$key]['status_title'] = OnlineProduct::getProductStatusAll($val['status']);
                $list[$key]['refund_start_date'] = date('Y-m-d', $val['start_date']);
                $list[$key]['refund_end_date'] = date('Y-m-d', $val['end_date']);
                if($status==6){
                    $dat = OnlineRepaymentRecord::find()->where(['online_pid'=>$val['id']])->select('created_at')->one();
                    $list[$key]['refund_time'] = date('Y-m-d', $dat->created_at);
                }else if($status==5){
                    $min_time = \common\models\order\OnlineFangkuanDetail::find()->where(['online_product_id' => $val['id']])->select('order_time')->min('order_time');
                    $earlier = $pp->LoanTerms('d1', date('Y-m-d', $min_time), $val['expires']);
                    $list[$key]['refund_time'] = $earlier;
                }else{
                    $list[$key]['refund_time'] = "";
                }
                $list[$key]['refund_method_title'] = OnlineProduct::getRefundMethod($val['refund_method']);
            }
            return ['res' => 1, 'data' => $list, 'msg' => 'success'];
        }
    }

    public function actionContract($order_id = null,$op = 'I'){
        if (\Yii::$app->user->isGuest) {
            return $this->redirect('/');
        }
        $contract = Contract::find()->where(['type'=>2,'contract_number'=>$order_id])->select("*")->one();
        
        set_time_limit(0);

        require_once "../../common/components/tcpdf/tcpdf.php";         
        
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        //$pdf->SetTitle="aaa";
        //echo $contract->contract_content;exit;
        //$pdf->SetFont('stsongstdlight', '', 5);
        $pdf->SetHeaderData('', '',$contract->contract_name, '');

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

        $pdf->MultiCell(0, '', $txt, 0, 'L', $fill=0, $ln=1, '', '', 0, true, true,  0);
        //MultiCell(宽, 高, 内容, 边框,文字对齐, 文字底色, 是否换行, x坐标, y坐标, 变高, 变宽, 是否支持html, 自动填充, 最大高度)

        $file_name = $contract->contract_number;//不知中文怎么支持
        $pdf->Output($file_name.".pdf", $op); /* 默认是I：在浏览器中打开，D：下载，F：在服务器生成pdf */
   
    }
    
}
