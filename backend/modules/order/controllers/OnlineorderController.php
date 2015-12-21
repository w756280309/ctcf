<?php

namespace backend\modules\order\controllers;

use Yii;
use common\models\user\User;
use yii\data\Pagination;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\lib\bchelp\BcRound;
use backend\controllers\BaseController;
use backend\modules\user\core\v1_0\UserAccountBackendCore;

/**
 * OrderController implements the CRUD actions for OfflineOrder model.
 */
class OnlineorderController extends BaseController {

    public function actionList($id=null) {//取出金额总计
        $username = Yii::$app->request->get('username');
        $mobile = Yii::$app->request->get('mobile');
        
        $query = OnlineOrder::find()->where(['online_pid' => $id]);
        $data = clone $query;
        
        //已筹集金额
        $moneyTotal = $query->sum('order_money');
        //剩余可投金额
        $biao = OnlineProduct::findOne($id);
        $shengyuKetou = bcsub($biao->money, $moneyTotal, 2);
        //已投资人数
        $count = $query->groupBy('uid')->count();
        //募捐时间
        $time = ( time() - ($biao->start_date)); //秒数
        $day = floor($time / (24 * 3600)); //天数
        $hour = floor(($time - $day * 24 * 3600) / 3600); //小时
        $mintus = floor(($time - $day * 24 * 3600 - $hour * 60 * 60) / 60); //分钟 都是余数！！！！
        $mujuanTime = "    " . $day . "   天   " . $hour . "   小时   " . $mintus . "   分";
        
        if(!empty($username)) {
            $data->andFilterWhere(['like','username', $username]);
        }
        
        if(!empty($mobile)) {
            $data->andFilterWhere(['like','mobile', $mobile]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '10']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        return $this->render('liebiao', [
                    'model' => $model,
                    'pages' => $pages,
                    'moneyTotal' => $moneyTotal,
                    'shengyuKetou' => $shengyuKetou,
                    'renshu' => $count,
                    'mujuanTime' => $mujuanTime
        ]);
    }

    public function actionDetailr($id=null,$type=null) {
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');
        $title = Yii::$app->request->get('title');
        
        $query = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id]);
        if(!empty($status)) {
            $query->andWhere(['status' => $status]);
        }
        if(!empty($title)) {
            $query->andFilterWhere(['like','title',$title]);
        }
//        if (isset($status) && $status !== '' && !empty($title)) {
//            $query = "del_status=0 and status='$status' and borrow_uid=$id and title like '%$title%'";
//        } elseif (isset($status) && $status !== '' && empty($title)) {
//            $query = "del_status=0 and status='$status' and borrow_uid=$id";
//        } else {
//            $query = "del_status=0 and borrow_uid=$id";
//        }
//        $query = OnlineProduct::find()->where($query);
        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time . " 23:59:59")]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time . " 0:00:00")]);
        }
        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->asArray()->all();

        //取出企业名
        $org_name = User::find()->where(['id' => $id])->select('org_name')->one();

        $ooc = new UserAccountBackendCore();
        $product = $ooc->getProduct($id);
        //取出融资金额总计，
        $moneyTotal = $product['sum'];
        //融资的次数
        $Num = $product['count'];
        //渲染到静态页面
        return $this->render('listr', [
                    'id' => $id,
                    'type' => $type,
                    'model' => $model,
                    'pages' => $pages,
                    'org_name' => $org_name['org_name'],
                    'moneyTotal' => $moneyTotal,
                    'Num' => $Num,
        ]);
    }

    public function actionDetailt($id = null, $type = null) {
        $status = Yii::$app->request->get('status'); 
        $time = Yii::$app->request->get('time');
        
        $query = OnlineOrder::find()->where(['uid' => $id]);
        if($status != null) {
            $query->andWhere(['status' => $status]);
        }
//        if (isset($status) && $status !== '') {
//            $query = "status='$status' and uid=$id";
//        } else {
//            $query = "uid=$id";
//        }
       // $query = OnlineOrder::find()->where($query);
        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time . " 23:59:59")]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time . " 0:00:00")]);
        }
        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        //取出用户名
        $username = User::find()->where(['id' => $id])->select('username')->one();
        //取出投资金额总计，应该包括充值成功的和充值失败的
//        $moneyTotal = OnlineOrder::find()->where(['uid' => $id])->sum('order_money');
//        //充值成功的次数
//        $successNum = OnlineOrder::find()->where(['uid' => $id, 'status' => 1])->count();
//        //投资失败的次数
//        $failureNum = OnlineOrder::find()->where(['uid' => $id, 'status' => 2])->count();
//        //项目名称
//        $online_ids = OnlineOrder::find()->where(['uid' => $id])->select("id,online_pid")->asArray()->all();
        
        $moneyTotal = 0;  //提现总额
        $successNum = 0;  //成功笔数
        $failureNum = 0;  //失败笔数
        $numdata = OnlineOrder::find()->where(['uid' => $id])->select('id,order_money,online_pid,status')->asArray()->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($numdata as $data){
            $moneyTotal = bcadd($moneyTotal,$data['order_money']);
            if($data['status']==OnlineOrder::STATUS_SUCCESS){
                $successNum++;
            }else if($data['status']==OnlineOrder::STATUS_CANCEL){
                $failureNum++;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal,2);
        
        //以online_order表中的id为下标online_id为单元值，组成一维数组        
        $result = OnlineProduct::findBySql("select oo.id,title from online_product op  left join online_order oo  on op.id=oo.online_pid where  oo.uid=$id")->asArray()->all();
        $res = [];
        foreach ($result as $v) {
            $res[$v['id']] = $v['title'];
        }
        //渲染到静态页面
        return $this->render('listt', [
                    'id' => $id,
                    'type' => $type,
                    'res' => $res,
                    'model' => $model,
                    'pages' => $pages,
                    'username' => $username['username'],
                    'moneyTotal' => $moneyTotal,
                    'successNum' => $successNum,
                    'failureNum' => $failureNum,
        ]);
    }

}
