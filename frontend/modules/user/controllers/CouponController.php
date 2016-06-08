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
    public function actionValid()
    {
        $sn = \Yii::$app->request->get('sn');
        $this->findOr404(OnlineProduct::class, ['sn' => $sn]);
        $monty = \Yii::$app->request->get('monty');
        if (!empty($monty) && !preg_match('/^[0-9|.]+$/', $monty)) {
            $this->ex404();
        }

        $ct = CouponType::tableName();
        $data = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['isUsed' => 0, 'order_id' => null, "$ct.isDisabled" => 0])
            ->andWhere(['<=', "$ct.useStartDate", date('Y-m-d')])
            ->andWhere(['>=', "$ct.useEndDate", date('Y-m-d')])
            ->andWhere(['user_id' => $this->getAuthedUser()->id])
            ->orderBy("$ct.useEndDate desc, $ct.amount desc, $ct.minInvest asc")
            ->all();
        return $this->renderFile('@frontend/modules/user/views/coupon/_valid_coupon.php', [
            'data' => $data
        ]);
    }
}

