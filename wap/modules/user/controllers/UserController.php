<?php

namespace app\modules\user\controllers;

use Yii;
use app\controllers\BaseController;
use yii\web\Response;
use yii\data\Pagination;
use common\core\UserAccountCore;
use common\models\user\MoneyRecord;
use common\service\OrderService;
use common\service\BankService;

class UserController extends BaseController {

    public function actionIndex(){
        $this->layout='account';
        $uacore = new UserAccountCore();
        $ua = $uacore->getUserAccount($this->uid);
        $leijishouyi = $uacore->getTotalProfit($this->uid);//累计收益
        $dhsbj = $uacore->getTotalWaitMoney($this->uid);//带回收本金
        $zcze = $uacore->getTotalFund($this->uid);//资产总额=理财资产+可用余额+冻结金额

        $data = BankService::checkKuaijie($this->uid);

        return $this->render('index',['ua'=>$ua,'user'=>$this->user,'ljsy'=>$leijishouyi,'dhsbj'=>$dhsbj,'zcze'=>$zcze, 'data' => $data]);
    }

    /**
     * 输出个人交易明细记录
     * 输出信息均为成功记录，仅包括四类：充值、提现、投资、还款
     */
    public function actionMingxi($page = 1, $size = 10) {
        $this->layout = "@app/modules/order/views/layouts/buy";
        $type = [MoneyRecord::TYPE_RECHARGE, MoneyRecord::TYPE_DRAW, MoneyRecord::TYPE_ORDER, MoneyRecord::TYPE_HUANKUAN];
        $data = MoneyRecord::find()->where(['uid' => $this->uid, 'type' => $type, 'status' => MoneyRecord::STATUS_SUCCESS])
            ->select('created_at,type,in_money,out_money,balance,status')
            ->orderBy('id desc');
        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        foreach ($model as $key => $val) {
            $model[$key]['created_at_date'] = date('Y-m-d',$val['created_at']);
            $model[$key]['created_at_time'] = date('H:i:s',$val['created_at']);
            $model[$key]['type'] = Yii::$app->params['mingxi'][$val['type']];
            if ($val['type'] == 0 || $val['type'] == 4) {
                $model[$key]['in_money'] = "+".$val['in_money'];
            } elseif ($val['type'] == 1 || $val['type'] == 2) {
                $model[$key]['out_money'] = "-".$val['out_money'];
            }
        }
        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $message = ($page > $tp) ? '数据错误' : '消息返回';
            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('mingxi', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    public function actionMyorder($type = null, $page = 1) {
        $this->layout = "@app/modules/user/views/layouts/myorder";
        $os = new OrderService();
        $list = $os->getUserOrderList($this->uid, $type, $page);
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $list;
        }
        return $this->render('order', ['list' => $list, 'type' => $type]);
    }

}
