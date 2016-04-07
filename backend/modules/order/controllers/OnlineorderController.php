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
class OnlineorderController extends BaseController
{
    public function actionList($id = null)
    {
        //取出金额总计
        $username = Yii::$app->request->get('username');
        $mobile = Yii::$app->request->get('mobile');

        $query = OnlineOrder::find()->where(['online_pid' => $id, 'status' => OnlineOrder::STATUS_SUCCESS]);
        $data = clone $query;

        //已筹集金额
        $moneyTotal = $query->sum('order_money');
        //剩余可投金额
        $biao = OnlineProduct::findOne($id);
        $shengyuKetou = bcsub($biao->money, $moneyTotal, 2);
        //已投资人数
        $count = $query->groupBy('uid')->count();
        //募捐时间
        $time = (time() - ($biao->start_date)); //秒数
        $day = floor($time / (24 * 3600)); //天数
        $hour = floor(($time - $day * 24 * 3600) / 3600); //小时
        $mintus = floor(($time - $day * 24 * 3600 - $hour * 60 * 60) / 60); //分钟 都是余数！！！！
        $mujuanTime = '    '.$day.'   天   '.$hour.'   小时   '.$mintus.'   分';

        if (!empty($username)) {
            $data->andFilterWhere(['like', 'username', $username]);
        }

        if (!empty($mobile)) {
            $data->andFilterWhere(['like', 'mobile', $mobile]);
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
                    'mujuanTime' => $mujuanTime,
                    'id'=>$id
        ]);
    }

    /**
     * 导出指定标的投记录
     * @param $id
     */
    public function actionExport($id)
    {
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=投标记录（" . $id . "）-" . date('Ymd') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        $lists = OnlineOrder::find()->where(['online_pid' => $id, 'status' => OnlineOrder::STATUS_SUCCESS])->limit(10000)->asArray()->all();
        $str = "<table border='1'><tr><th style='width: 200px;'>编号</th><th>真实姓名</th><th>手机号</th><th>投资金额（元）</th><th>投资时间</th><th>状态</th>";
        if ($lists) {
            foreach ($lists as $list) {
                if ($list['status'] == 0) {
                    $status = "投标失败";
                } elseif ($list['status'] == 1) {
                    $status = "投标成功";
                } elseif ($list['status'] == 2) {
                    $status = "撤标";
                } else {
                    $status = "无效";
                }
                $str .= "<tr>
                <td style=\"vnd.ms-excel.numberformat:@\">" . strval($list['sn']) . "</td>
                <td>" . $list['username'] . "</td>
                <td>" . $list['mobile'] . "</td>
                <td>" . $list['order_money'] . "</td>
                <td>" . date('Y-m-d H:i:s', $list['created_at']) . "</td>
                <td>" . $status . "</td>
                </tr>";
            }
        }
        $str .= "</table>";
        $str = mb_convert_encoding($str, "GB2312", "UTF-8");
        echo $str;
    }

    /**
     * 融资明细页面展示
     */
    public function actionDetailr($id, $type)
    {
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');
        $title = Yii::$app->request->get('title');

        $query = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $id]);

        if (!empty($status)) {
            if ($status === '-1') {
                $query->andWhere(['online_status' => OnlineProduct::STATUS_PREPARE]);
            } else {
                $query->andWhere(['status' => $status]);
            }
        }
        if (!empty($title)) {
            $query->andFilterWhere(['like', 'title', $title]);
        }

        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time.' 23:59:59')]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time.' 0:00:00')]);
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
            'status' => $status,
            'title' => $title,
            'time' => $time,
        ]);
    }

    public function actionDetailt($id = null, $type = null)
    {
        $status = Yii::$app->request->get('status');
        $time = Yii::$app->request->get('time');

        $query = OnlineOrder::find()->where(['uid' => $id]);
        if ($status != null) {
            $query->andWhere(['status' => $status]);
        }

        if (!empty($time)) {
            $query->andFilterWhere(['<', 'created_at', strtotime($time.' 23:59:59')]);
            $query->andFilterWhere(['>=', 'created_at', strtotime($time.' 0:00:00')]);
        }

        //正常显示详情页
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->all();

        //取出用户名
        $username = User::find()->where(['id' => $id])->select('username')->one();

        //取出投资金额总计，应该包括充值成功的和充值失败的
        $moneyTotal = 0;  //提现总额
        $successNum = 0;  //成功笔数
        $failureNum = 0;  //失败笔数
        $numdata = OnlineOrder::find()->where(['uid' => $id])->select('id,order_money,online_pid,status')->asArray()->all();
        $bc = new BcRound();
        bcscale(14);
        foreach ($numdata as $data) {            
            if ($data['status'] == OnlineOrder::STATUS_SUCCESS) {
                $moneyTotal = bcadd($moneyTotal, $data['order_money']);
                ++$successNum;
            } elseif ($data['status'] == OnlineOrder::STATUS_CANCEL) {
                ++$failureNum;
            }
        }
        $moneyTotal = $bc->bcround($moneyTotal, 2);

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
