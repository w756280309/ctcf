<?php

namespace frontend\modules\user\controllers;

use common\models\coupon\CouponType;
use common\models\product\OnlineProduct;
use frontend\controllers\BaseController;
use common\models\coupon\UserCoupon;
use yii\data\Pagination;
use yii\filters\AccessControl;

class CouponController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 我的代金券.
     */
    public function actionIndex()
    {
        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $_query = clone $query;

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query->offset($pages->offset)->limit($pages->limit)->orderBy('isUsed asc, useEndDate desc, amount desc')->all();

        $data = $_query->select("count(*) as count, sum(amount) as totalAmount")
            ->andWhere(['isUsed' => 0, 'order_id' => null])
            ->andWhere(['>=', 'useEndDate', date('Y-m-d')])
            ->createCommand()
            ->queryone();

        return $this->render('index', ['model' => $model, 'pages' => $pages, 'data' => $data]);
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
            $this->ex404();
        }

        if (!empty($request['money']) && !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $this->ex404();
        }

        $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $data = CouponType::find()    //获取有效的代金券信息
        ->select("$ct.*, $uc.user_id, $uc.order_id, $uc.isUsed, $uc.id uid")
            ->innerJoin($uc, "$ct.id = $uc.couponType_id")
            ->where(['isUsed' => 0, 'order_id' => null, 'isDisabled' => 0])
            ->andFilterWhere(['<=', 'useStartDate', date('Y-m-d')])
            ->andFilterWhere(['>=', 'useEndDate', date('Y-m-d')])
            ->andWhere(['user_id' => $this->getAuthedUser()->id])
            ->orderBy('useEndDate desc, amount desc, minInvest asc');

        $pg = \Yii::$container->get('paginator')->paginate($data, $page, $size);
        $coupon = $pg->getItems();

        foreach ($coupon as $key => $val) {
            $coupon[$key]['minInvestDesc'] = \Yii::$app->functions->toFormatMoney(rtrim(rtrim($val['minInvest'], '0'), '.'));
        }

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;

        $message = ($page > $tp) ? '数据错误' : '消息返回';

        return ['header' => $pg, 'data' => $coupon, 'code' => $code, 'message' => $message];
    }
}

