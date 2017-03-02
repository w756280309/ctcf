<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\promo\InviteRecord;
use common\models\user\MoneyRecord;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;

class InviteController extends BaseController
{
    /**
     * 邀请好友页面.
     */
    public function actionIndex($page = 1)
    {
        $user = $this->getAuthedUser();
        $model = InviteRecord::getInviteRecord($user);

        $pageSize = 5;
        $count = count($model);

        $data = new ArrayDataProvider([
            'allModels' => $model,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $tp = $pages->pageCount;
        $header = [
            'count' => $count,
            'size' => $pageSize,
            'tp' => $tp,
            'cp' => intval($page),
        ];
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $html = $this->renderFile('@wap/modules/user/views/invite/list.php', ['data' => $data->getModels()]);
            return ['header' => $header, 'html' => $html, 'code' => $code, 'message' => $message];
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
            'pages' => $pages,
            'user' => $user,
            'cash' => bcsub($totalCash, $promoCash, 2),
        ]);
    }
}
