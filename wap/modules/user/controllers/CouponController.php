<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use yii\web\Response;

class CouponController extends BaseController
{
    /**
     * 我的代金券.
     */
    public function actionList($page = 1, $size = 10)
    {
        $c = CouponType::tableName();

        $data = UserCoupon::find()
            ->select("$c.*, isUsed")
            ->innerJoin($c, "couponType_id = $c.id")
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0])
            ->orderBy('isUsed asc, useEndDate desc, amount desc');

        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $model = $pg->getItems();

        foreach ($model as $key => $val) {
            $model[$key]['minInvestDesc'] = \Yii::$app->functions->toFormatMoney(rtrim(rtrim($val['minInvest'], '0'), '.'));
        }

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $model, 'code' => $code, 'message' => $message];
        }

        return $this->render('list', ['model' => $model, 'header' => $pg->jsonSerialize()]);
    }

    /**
     * 可用代金券.
     */
    public function actionValid($page = 1, $size = 10)
    {
        $ct = CouponType::tableName();
        $uc = UserCoupon::tableName();

        $request = array_replace([
                'sn' => null,
                'money' => 0,
            ], \Yii::$app->request->get());

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw new \yii\web\NotFoundHttpException();
        }

        $product = OnlineProduct::findOne(['sn' => $request['sn']]);
        if (null === $product) {
            throw new \yii\web\NotFoundHttpException();
        }

        if (!empty($request['money']) && !preg_match('/^[0-9|.]+$/', $request['money'])) {
            throw new \yii\web\NotFoundHttpException();
        }

        $data = CouponType::find()    //获取有效的代金券信息
            ->select("$ct.*, $uc.user_id, $uc.order_id, $uc.isUsed, $uc.id uid")
            ->innerJoin($uc, "$ct.id = $uc.couponType_id")
            ->where(['isUsed' => 0, 'order_id' => null, 'isDisabled' => 0])
            ->andFilterWhere(['<=', 'useStartDate', date('Y-m-d')])
            ->andFilterWhere(['>=', 'useEndDate', date('Y-m-d')])
            ->andWhere(['user_id' => $this->getAuthedUser()->id]);

        if (0 !== doubleval($request['money'])) {
            $data->andFilterWhere(['<=', 'minInvest', $request['money']]);
        }

        $data->orderBy('useEndDate desc, amount desc, minInvest asc');

        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $coupon = $pg->getItems();

        foreach ($coupon as $key => $val) {
            $coupon[$key]['minInvestDesc'] = \Yii::$app->functions->toFormatMoney(rtrim(rtrim($val['minInvest'], '0'), '.'));
        }

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (\Yii::$app->request->isAjax) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pg, 'data' => $coupon, 'code' => $code, 'message' => $message];
        }

        return $this->render('valid_list', [
                'coupon' => $coupon,
                'sn' => $request['sn'],
                'money' => $request['money'],
                'header' => $pg->jsonSerialize(),
            ]);
    }
}
