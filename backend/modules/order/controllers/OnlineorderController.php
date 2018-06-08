<?php

namespace backend\modules\order\controllers;

use backend\controllers\BaseController;
use backend\modules\user\core\v1_0\UserAccountBackendCore;
use common\jobs\MiitBaoQuanJob;
use common\lib\user\UserStats;
use common\models\user\User;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class OnlineorderController extends BaseController
{
    public function actionList($id)
    {
        if (empty($id)) {
            throw $this->ex404();   //参数无效时,抛出404异常
        }

        //取出金额总计
        $username = Yii::$app->request->get('username');
        $mobile = Yii::$app->request->get('mobile');

        $u = User::tableName();
        $ol = OnlineOrder::tableName();
        $query = OnlineOrder::find()
            ->innerJoin('user' , "$u.id = $ol.uid")
            ->where([
            "$ol.online_pid" => $id,
            "$ol.status" => OnlineOrder::STATUS_SUCCESS
        ]);
        $data = clone $query;

        //已筹集金额
        $moneyTotal = $query->sum('order_money');
        //剩余可投金额
        $biao = OnlineProduct::findOne($id);
        $shengyuKetou = bcsub($biao->money, $moneyTotal, 2);
        //已投资人数
        $count = $query->select('uid')->groupBy('uid')->count();
        //募捐时间
        $time = (time() - ($biao->start_date)); //秒数
        $day = floor($time / (24 * 3600)); //天数
        $hour = floor(($time - $day * 24 * 3600) / 3600); //小时
        $mintus = floor(($time - $day * 24 * 3600 - $hour * 60 * 60) / 60); //分钟 都是余数！！！！
        $mujuanTime = '    '.$day.'   天   '.$hour.'   小时   '.$mintus.'   分';

        if (!empty($username)) {
            $data->andFilterWhere(['like', "$ol.username", $username]);
        }

        if (!empty($mobile)) {
            $data->andFilterWhere(['like', "$u.safeMobile", SecurityUtils::encrypt($mobile)]);
        }
        $data = $data->orderBy(['id' => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $data,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);

        return $this->render('liebiao', [
            'dataProvider' => $dataProvider,
            'moneyTotal' => $moneyTotal,
            'shengyuKetou' => $shengyuKetou,
            'renshu' => $count,
            'mujuanTime' => $mujuanTime,
            'id' => $id,
            'loan' => $biao,
        ]);
    }

    /**
     * 导出指定标的投记录
     * @param $id
     */
    public function actionExport($id)
    {
        $loan = $this->findOr404(OnlineProduct::className(), $id);
        $u = User::tableName();
        $o = OnlineOrder::tableName();
        $lists = OnlineOrder::find()
            ->select(['sn', "$o.status", "$o.username", "$u.safeMobile", 'order_money', 'yield_rate', "$u.created_at as regAt", "$o.created_at", "$u.safeIdCard"])
            ->where(["$o.online_pid" => $id, "$o.status" => OnlineOrder::STATUS_SUCCESS])
            ->innerJoin($u, "$o.uid = $u.id")
            ->asArray()
            ->all();
        $exportData[] = ["编号", "真实姓名", "手机号", "身份证", "投资金额（元）", "客户年化率（%）", "注册时间", "投资时间", "状态"];
        if (0 !== count($lists)) {
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
                $exportData[] = [
                    strval($list['sn']),
                    $list['username'],
                    floatval(SecurityUtils::decrypt($list['safeMobile'])),
                    $list['safeIdCard'] ? substr(SecurityUtils::decrypt($list['safeIdCard']), 0, 14) . '****' : '',
                    floatval($list['order_money']),
                    StringUtils::amountFormat2(bcmul($list['yield_rate'], 100, 2)),
                    date('Y-m-d H:i:s', $list['regAt']),
                    date('Y-m-d H:i:s', $list['created_at']),
                    $status
                ];
            }
        }
        UserStats::exportAsXlsx($exportData, $loan->title.'投资记录('.date('Y-m-d H:i:s').').xlsx');
    }

    /**
     * 融资明细页面展示
     */
    public function actionDetailr($id, $type)
    {
        if (empty($id) || empty($type) || !in_array($type, [1, 2])) {
            throw $this->ex404();   //参数无效时,抛出404异常
        }

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

    /**
     * 投资用户交易明细页面.
     */
    public function actionDetailt($id)
    {
        if (empty($id)) {
            throw $this->ex404();   //参数无效时,抛出404异常
        }
        $user = $this->findOr404(User::class, $id);
        $status = Yii::$app->request->get('status');
        $loan_status = Yii::$app->request->get('loan_status');
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $query = OnlineOrder::find()->where(['online_order.uid' => $id]);
        if ($status != null) {
            $query->andWhere(['online_order.status' => $status]);
        }
        if ($loan_status != null) {
            $query->leftJoin('online_product', 'online_product.id = online_order.online_pid');
            $query->andWhere(['online_product.status' => $loan_status]);
        }
        if (!empty($start)) {
            $query->andFilterWhere(['>=', 'online_order.created_at', strtotime($start.' 0:00:00')]);
        }
        if (!empty($end)) {
            $query->andFilterWhere(['<=', 'online_order.created_at', strtotime($end.' 23:59:59')]);
        }
        $query->orderBy(['online_order.created_at' =>SORT_DESC]);
        $query->with('loan');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $loanStatus = Yii::$app->params['deal_status'];

        return $this->renderFile('@backend/modules/order/views/onlineorder/listt.php', [
            'dataProvider' => $dataProvider,
            'user' => $user,
            'loanStatus' => $loanStatus
        ]);
    }

    /**
     * 下载和签保全合同
     * @param $id
     * @return array|\yii\web\NotFoundHttpException
     */
    public function actionMiitBaoquan($id)
    {
        $order = OnlineOrder::findOne($id);
        //订单不存在
        if (is_null($order)) {
            throw $this->ex404();
        }

        $url = $order->getMiitViewUrl();
        //成功保全的直接返回查看链接
        if (!empty($url)) {
            return ['code' => 1, 'url' => $url];
        }

        //保全失败的，重新保全
        if (Yii::$app->params['enable_miitbaoquan']) {
            Yii::$app->queue->push(new MiitBaoQuanJob([
                'order' => $order,
                'item_type' => 'loan_order',
            ]));
        }

        return ['code' => 0, 'message' => '合同生成中，请稍后'];
    }

    /**
     * 下载易保全合同
     * @param $id
     * @return array|\yii\web\NotFoundHttpException
     */
    public function actionEbaoquan($id)
    {
        $order = OnlineOrder::findOne($id);
        //订单不存在
        if (is_null($order)) {
            throw $this->ex404();
        }

        $url = $order->getBaoquanDownloadLink();
        //成功保全的直接返回查看链接
        if (!empty($url)) {
            return ['code' => 1, 'url' => $url];
        }

        //保全失败的，返回失败
        return ['code' => 0, 'message' => '保全失败，无法下载'];
    }
}