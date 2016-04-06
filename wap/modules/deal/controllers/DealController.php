<?php
namespace app\modules\deal\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\controllers\HelpersTrait;

class DealController extends Controller
{
    use HelpersTrait;
    /**
     * 行为设置，对于请求如果是ajax请求返回json.
     */
    public function behaviors()
    {
        return [
            'requestbehavior' => [
                'class' => 'common\components\RequestBehavior',
            ],
            \common\filters\AppAcesssControl::className(),
        ];
    }

    /**
     * 获取理财列表.
     */
    public function actionIndex($page = 1, $cat = 1, $xs = null)
    {
        if (null !== $cat) {
            $cat = (int) $cat;
        }
        $cat_ids = array_keys(Yii::$app->params['pc_cat']);
        if ((null !== $cat && !in_array($cat, $cat_ids)) || (null !== $xs && !in_array($xs, [0, 1]))) {
            throw new \yii\web\BadRequestHttpException('参数无效');
        }

        $cond = ['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE];

        if (null !== $xs) {
            $cond['is_xs'] = $xs;
        } else {
            $cond['cid'] = $cat;
            $cond['is_xs'] = 0;
        }
        $data = OnlineProduct::find()->where($cond)->select('id k,sn as num,title,yield_rate as yr,status,expires as qixian,money,start_date as start,finish_rate,jiaxi,start_money,refund_method');
        $count = $data->count();
        $size = 5;
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);
        $deals = $data->offset(($page - 1) * $size)->limit($pages->limit)->orderBy('recommendTime desc,sort asc,id desc')->asArray()->all();
        foreach ($deals as $key => $val) {
            $dates = Yii::$app->functions->getDateDesc($val['start']);
            $deals[$key]['start'] = date('H:i', $val['start']);
            $deals[$key]['start_desc'] = $dates['desc'];
            $deals[$key]['finish_rate'] = number_format($val['finish_rate'] * 100, 0);
            $deals[$key]['yr'] = $val['yr'] ? OnlineProduct::calcBaseRate($val['yr'], $val['jiaxi']) : '0.00';
            $deals[$key]['statusval'] = Yii::$app->params['productonline'][$val['status']];
            $deals[$key]['jiaxi'] = $val['jiaxi'];
            $deals[$key]['method'] = (1 === (int)$val['refund_method']) ? "天" : "个月";
            $deals[$key]['start_money'] = $val['start_money'];
            $deals[$key]['cid'] = \Yii::$app->params['refund_method'][$val['refund_method']];
        }
        $tp = ceil($count / $size);
        $code = ($page > $tp) ? 1 : 0;

        $header = [
            'cat' => $cat,
            'xs' => $xs,
            'count' => intval($count),
            'size' => $size,
            'tp' => $tp,
            'cp' => intval($page),
        ];

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $header, 'deals' => $deals, 'code' => $code, 'message' => $message];
        }

        return $this->render('index', ['deals' => $deals, 'header' => $header]);
    }

    /**
     * 标的详情页面.
     * @param type $sn
     * @return type
     */
    public function actionDetail($sn)
    {
        if (empty($sn)) {
            throw new \yii\web\ServerErrorHttpException('标的编号不能为空');
        }
        $deals = OnlineProduct::findOne(['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        if (empty($deals)) {
            throw new \yii\web\NotFoundHttpException('您访问的页面找不到');
        }
        $orderbalance = 0;
        if (OnlineProduct::STATUS_FOUND === (int) $deals['status']) {
            $orderbalance = 0;
        } else if ($deals['status'] >= OnlineProduct::STATUS_NOW) {
            //募集期的取剩余
            $orderbalance = $deals->getLoanBalance(); //项目可投余额
        } else {
            $orderbalance = $deals['money'];
        }

        if ($deals['status'] == OnlineProduct::STATUS_PRE) {
            $start = Yii::$app->functions->getDateDesc($deals['start_date']);
            $deals['start_date'] = $start['desc'].date('H:i', $start['time']);
        }
        if ($deals['status'] == OnlineProduct::STATUS_HUAN || $deals['status'] == OnlineProduct::STATUS_OVER || $deals['status'] == OnlineProduct::STATUS_FOUND) {
            $deals['finish_rate'] = 1;
        }

        return $this->render('detail', ['deal' => $deals, 'deal_balace' => $orderbalance]);
    }

    /**
     * 获取投资记录.
     * @param type $pid
     * @return type
     */
    public function actionOrderlist($pid = null)
    {
        if (empty($pid)) {
            return ['orders' => [], 'code' => 1, 'message' => 'pid参数不能为空'];
        }
        $data = OnlineOrder::getOrderListByCond(['online_pid' => $pid, 'status' => 1], 'mobile,order_time time,order_money money');
        foreach ($data as $key => $dat) {
            $data[$key]['mobile'] = substr_replace($dat['mobile'], '****', 3, 4);
            $data[$key]['time'] = date('Y-m-d', $dat['time']);
            $data[$key]['his'] = date('H:i:s', $dat['time']);
            $data[$key]['money'] = doubleval($dat['money']);
        }

        return ['orders' => $data, 'code' => 0, 'message' => '消息返回'];
    }

    /**
     * 立即认购.
     * @param type $sn 标的sn
     */
    public function actionToorder($sn = null)
    {
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->toCart($this->getAuthedUser(), $sn);

        return $ret;
    }
}
