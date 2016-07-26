<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use yii\data\Pagination;

class CouponController extends BaseController
{
    /**
     * 我的代金券.
     */
    public function actionList($page = 1)
    {
        $c = CouponType::tableName();
        $uc = UserCoupon::tableName();

        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => '10']);
        $model = $query
            ->select("$c.amount, $c.name, $c.minInvest, if($uc.isUsed, bin(0), $uc.expiryDate < date(now())) as isExpired, $uc.expiryDate, $uc.isUsed, $uc.couponType_id")
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy("isExpired, isUsed, $uc.expiryDate, amount desc, minInvest")
            ->asArray()
            ->all();

        foreach ($model as $key => $val) {
            $model[$key]['minInvestDesc'] = StringUtils::amountFormat1('{amount}{unit}', $val['minInvest']);
        }

        $tp = $pages->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        if (\Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return ['header' => $pages, 'data' => $model, 'code' => $code, 'message' => $message, 'tp' => $tp, 'cp' => $page];
        }

        return $this->render('list', ['model' => $model, 'header' => $pages]);
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
                'money' => null,
            ], \Yii::$app->request->get());

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (!empty($request['money']) && !preg_match('/^[0-9|.]+$/', $request['money'])) {
            throw $this->ex404();
        }

        $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $data = UserCoupon::find()
            ->select("$ct.name, $ct.amount, $ct.minInvest, $uc.id uid, order_id, isUsed, expiryDate")
            ->innerJoin($ct, "couponType_id = $ct.id")
            ->where(['isUsed' => 0, 'isDisabled' => 0, 'user_id' => $this->getAuthedUser()->id])
            ->andFilterWhere(['>=', 'expiryDate', date('Y-m-d')])
            ->orderBy('expiryDate, amount desc, minInvest');

        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $coupon = $pg->getItems();

        foreach ($coupon as $key => $val) {
            $coupon[$key]['minInvestDesc'] = \Yii::$app->functions->toFormatMoney(rtrim(rtrim($val['minInvest'], '0'), '.'));
        }

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        if (\Yii::$app->request->isAjax) {
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
