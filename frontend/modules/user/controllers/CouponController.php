<?php

namespace frontend\modules\user\controllers;

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
}

