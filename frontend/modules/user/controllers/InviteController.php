<?php

namespace frontend\modules\user\controllers;

use common\models\promo\InviteRecord;
use common\models\user\MoneyRecord;
use frontend\controllers\BaseController;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\filters\AccessControl;

class InviteController extends BaseController
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
     * 邀请好友页面.
     * 1. 每页显示5条记录;
     * 2. 翻页方式改为Ajax形式;
     */
    public function actionIndex()
    {
        $this->layout = 'main';
        $pageSize = 5;
        $user = $this->getAuthedUser();
        $model = InviteRecord::getInviteRecord($user);

        $data = new ArrayDataProvider([
            'allModels' => $model,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => count($model), 'pageSize' => $pageSize]);

        if (Yii::$app->request->isAjax) {
            return $this->renderFile('@frontend/modules/user/views/invite/_list.php', ['model' => $model, 'data' => $data->getModels(), 'pages' => $pages]);
        }

        //获取用户累计收到的现金红包
        //目前（2017-02-28）双十二活动(promo_12_12_21)及邀请好友活动(promo_invite_12)有发现金红包, 用用户收到的所有现金红包总金额 减去 用户双十二活动得到的现金红包 （reward_id 为 7,8,9,10 的金额对应的现金红包分别为 2,3,4,5元）
        //用户收到的现金红包金额, money_record 对应的 type = MoneyRecord::TYPE_CASH_GIFT
        $totalCash = MoneyRecord::find()->where(['uid' => $user->id, 'type' => MoneyRecord::TYPE_CASH_GIFT])->sum('in_money');
        //用户双十二活动得到的现金红包 （key = promo_12_12_21 reward_id 为 7,8,9,10 的金额对应的现金红包分别为 2,3,4,5元）
        $promo = RankingPromo::findOne(['key' => 'promo_12_12_21']);
        if (!is_null($promo)) {
            $sql = "SELECT SUM( 
CASE reward_id
WHEN 7 THEN 2 
WHEN 8 THEN 3 
WHEN 9 THEN 4 
WHEN 10 THEN 5 
END ) AS s
FROM  `promo_lottery_ticket` 
WHERE promo_id = :promoId
AND isRewarded =1
AND user_id = :userId";
            $res =Yii::$app->db->createCommand($sql, ['promoId' => $promo->id, 'userId' => $user->id])->queryScalar();
            $promoCash = floatval($res);
        } else {
            $promoCash = 0;
        }


        return $this->render('index', [
            'model' => $model,
            'data' => $data->getModels(),
            'user' => $user,
            'pages' => $pages,
            'cash' => bcsub($totalCash, $promoCash, 2),
        ]);
    }
}

