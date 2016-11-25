<?php
namespace app\modules\deal\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\controllers\HelpersTrait;
use common\utils\StringUtils;
use common\models\product\RateSteps;
use yii\web\NotFoundHttpException;

class DealController extends Controller
{
    use HelpersTrait;

    /**
     * 获取理财列表.
     */
    public function actionIndex($page = 1)
    {
        $size = 5;

        $query = OnlineProduct::find()
            ->select('*')
            ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE
            ]);

        $count = $query->count();

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $size]);

        $deals = $query->offset($pages->offset)->limit($pages->limit)
            ->orderBy('xs_status desc, recommendTime desc, sort asc, finish_rate desc, id desc')
            ->all();

        $tp = ceil($count / $size);
        $code = ($page > $tp) ? 1 : 0;

        $header = [
            'count' => intval($count),
            'size' => $size,
            'tp' => $tp,
            'cp' => intval($page),
        ];

        if (Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';
            $html = $this->renderFile('@wap/modules/deal/views/deal/_more.php', ['deals' => $deals, 'header' => $header]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
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
        if (empty($sn)) {    //参数无效时,抛出异常
            throw new \yii\web\ServerErrorHttpException('标的编号不能为空');
        }

        $deals = OnlineProduct::findOne(['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        if (null === $deals) {   //对象没有查到时,抛出异常
            throw new NotFoundHttpException();
        }
        //未登录或者登录了，但不是定向用户的情况下，报404
        if (1 === $deals->isPrivate) {
            if (Yii::$app->user->isGuest) {
                throw new NotFoundHttpException();
            } else {
                $uids = explode(',', $deals->allowedUids);
                if (!in_array(Yii::$app->user->identity->getId(), $uids)) {
                    throw new NotFoundHttpException();
                }
            }
        }

        $orderbalance = $deals->getLoanBalance(); //项目可投余额

        if ($deals->status == OnlineProduct::STATUS_PRE) {
            $start = Yii::$app->functions->getDateDesc($deals->start_date);
            $deals->start_date = $start['desc'].date('H:i', $start['time']);
        }

        return $this->render('detail', ['deal' => $deals, 'deal_balace' => $orderbalance]);
    }

    /**
     * 获取投资记录.
     * @param type $pid
     * @return type
     */
    public function actionOrderlist($pid)
    {
        if (empty($pid)) {
            return ['orders' => [], 'code' => 1, 'message' => 'pid参数不能为空'];
        }

        $data = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => 1])->select('mobile,order_time time,order_money money')->orderBy("id desc")->asArray()->all();
        foreach ($data as $key => $dat) {
            $data[$key]['mobile'] = StringUtils::obfsMobileNumber($dat['mobile']);
            $data[$key]['time'] = date('Y-m-d', $dat['time']);
            $data[$key]['his'] = date('H:i:s', $dat['time']);
            $data[$key]['money'] = rtrim(rtrim(number_format($dat['money'], 2), '0'), '.');
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
