<?php
namespace app\modules\order\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use common\models\product\OnlineProduct;
use common\models\contract\ContractTemplate;
use common\models\order\OnlineOrder;
use common\service\PayService;
use common\core\UserAccountCore;
use common\core\OrderCore;

class OrderController extends BaseController
{
    /**
     * 认购页面.
     * @param type $sn 标的编号
     * @return page
     */
    public function actionIndex($sn)
    {
        $this->layout = 'buy';

        $deal = OnlineProduct::findOne(['sn' => $sn]);
        if (empty($deal)) {
            throw new \yii\web\NotFoundHttpException('This production is not existed.');
        }
        $uacore = new UserAccountCore();
        $ua = $uacore->getUserAccount($this->user->id);
        $param['order_balance'] = OnlineOrder::getOrderBalance($deal->id); //计算标的可投余额;
        $param['my_balance'] = $ua->available_balance; //用户账户余额;

        return $this->render('index', ['deal' => $deal, 'param' => $param]);
    }

    /**
     * 购买标的.
     * @param type $sn
     * @return type
     */
    public function actionDoorder($sn = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $money = \Yii::$app->request->post('money');
        $trade_pwd = \Yii::$app->request->post('trade_pwd');
        $pay = new PayService(PayService::REQUEST_AJAX);
        $ret = $pay->checkAllowPay($this->user, $sn, $money);
        if ($ret['code'] != PayService::ERROR_SUCCESS) {
            return $ret;
        }
        $ordercore = new OrderCore();
        return $ordercore->createOrder($sn, $money,  $this->user->id);
    }

    public function actionOrdererror()
    {
        $this->layout = '@app/modules/order/views/layouts/buy';

        return $this->render('error');
    }

    public function actionAgreement($sn, $id, $key = 0)
    {
        $this->layout = false;

        if (empty($sn) || empty($id)) {
            throw new \yii\web\NotFoundHttpException('The argument err.');
        }

        $model = ContractTemplate::find()->where(['pid' => $id])->select('pid,name,content')->asArray()->all();

        return $this->render('agreement', ['model' => $model, 'key_f' => $key, 'content' => $model[$key]['content'], 'sn' => $sn]);
    }
}
