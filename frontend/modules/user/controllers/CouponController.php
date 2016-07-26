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
     * 1. 代金券排序规则:
     *  a. 状态: 未使用,已使用,已过期;
     *  b. 截止日期由近到远;
     *  c. 同一状态,同一时间,面值降序;
     *  d. 同一状态,同一时间,最小使用额升序;
     */
    public function actionIndex()
    {
        $this->layout = 'main';
        $uc = UserCoupon::tableName();

        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $_query = clone $query;

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query
            ->select("$uc.*, if($uc.isUsed, bin(0), $uc.expiryDate < date(now())) as isExpired")
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy("isExpired, isUsed, $uc.expiryDate, amount desc, minInvest")
            ->all();

        $data = $_query->select("count(*) as count, sum(amount) as totalAmount")
            ->andWhere(['isUsed' => 0])
            ->andFilterWhere(['>=', 'expiryDate', date('Y-m-d')])
            ->createCommand()
            ->queryone();

        return $this->render('index', ['model' => $model, 'pages' => $pages, 'data' => $data]);
    }
}

