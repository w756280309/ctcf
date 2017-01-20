<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;
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
     * 可用代金券列表.
     */
    public function actionValid($page = 1, $size = 10)
    {
        $request = $this->validateParams(Yii::$app->request->get());
        $query = $this->validCoupon($this->getAuthedUser());

        $pg = Yii::$container->get('paginator')->paginate($query, $page, $size);
        $coupons = $query
            ->offset($pg->offset)
            ->limit($pg->limit)
            ->all();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        $header = ['header' => $pg->jsonSerialize()];
        $backArr = [
            'coupons' => $coupons,
            'sn' => $request['sn'],
            'money' => $request['money'],
        ];

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_valid_list', $backArr);

            return [
                'header' => $pg->jsonSerialize(),
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        return $this->render('valid_list', array_merge($backArr, $header));
    }

    /**
     * 根据输入的金额自动获取代金券.
     */
    public function actionValidForLoan()
    {
        $request = $this->validateParams(Yii::$app->request->get());

        $coupon = $this->validCoupon($this->getAuthedUser(), $request['money'])->one();
        $this->layout = false;

        return $this->render('_valid_coupon', ['coupon' => $coupon]);
    }

    /**
     * 查询可用代金券.
     *
     * 1. 排序规则为到期时间的升序,金额的降序,起投金额的升序,ID的降序;
     */
    private function validCoupon($user, $money = null)
    {
        $uc = UserCoupon::tableName();

        return UserCoupon::validList($user, $money)
            ->orderBy([
                'expiryDate' => SORT_ASC,
                'amount' => SORT_DESC,
                'minInvest' => SORT_ASC,
                "$uc.id" => SORT_DESC,
            ]);
    }

    private function validateParams($get)
    {
        $request = array_replace([
            'sn' => null,
            'money' => null,
        ], $get);

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (empty($request['money']) || !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $request['money'] = null;
        }

        $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        return $request;
    }
}