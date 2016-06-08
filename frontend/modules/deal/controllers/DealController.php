<?php

namespace frontend\modules\deal\controllers;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;

class DealController extends BaseController
{

    public function actionDetail($sn)
    {
        $deal = $this->findOr404(OnlineProduct::className(), ['online_status' => OnlineProduct::STATUS_ONLINE, 'del_status' => OnlineProduct::STATUS_USE, 'sn' => $sn]);
        //未登录或者登录了，但不是定向用户的情况下，报404
        if (1 === $deal->isPrivate) {
            if (Yii::$app->user->isGuest) {
                $this->ex404('未登录用户不能查看定向标');
            } else {
                $user_ids = explode(',', $deal->allowedUids);
                if (!in_array(Yii::$app->user->identity->getId(), $user_ids)) {
                    $this->ex404('不能查看他人的定向标');
                }
            }
        }
        return $this->render('detail', [
            'deal' => $deal,
        ]);
    }

    public function actionOrderList($pid)
    {
        $this->findOr404(OnlineProduct::className(), ['id' => $pid]);
        $query = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => 1])->select('mobile,order_time,order_money')->orderBy("id desc");
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 20
        ]);
        $data = $query->offset($pages->offset)->limit($pages->limit)->all();
        return $this->renderFile('@frontend/modules/deal/views/deal/_order_list.php', [
            'data' => $data,
            'pages' => $pages,
        ]);
    }
}