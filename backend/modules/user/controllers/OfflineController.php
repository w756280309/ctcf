<?php

namespace backend\modules\user\controllers;

use backend\controllers\BaseController;
use common\models\mall\PointOrder;
use common\models\mall\PointRecord;
use common\models\offline\OfflinePointManager;
use common\models\offline\OfflineUser;
use common\utils\TxUtils;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;

class OfflineController extends BaseController
{
    /**
     * 线下会员列表
     */
    public function actionList()
    {
        $query = OfflineUser::find();
        $pages = new Pagination([
            'totalCount' => $query->count(),
            'pageSize' => 15,
        ]);
        $users = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id' => SORT_DESC])->all();

        return $this->render('list', ['users' => $users, 'pages' => $pages]);
    }

    /**
     * 线下会员详情页
     */
    public function actionDetail($id)
    {
        $user = OfflineUser::findOne($id);
        if (null === $user) {
            throw $this->ex404();
        }

        return $this->render('detail', ['user' => $user]);
    }

    /**
     * 线下会员详情页-积分流水
     */
    public function actionPoints($id)
    {
        $query = PointRecord::find()->where(['user_id' => $id, 'isOffline' => true])->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->renderFile('@backend/modules/user/views/offline/_point_record.php', ['dataProvider' => $dataProvider, 'id' => $id]);
    }

    /**
     * 商品兑换页面
     */
    public function actionExchangeGoods($id)
    {
        $user = $this->findOr404(OfflineUser::class, $id);
        return $this->render('exchange', ['user' => $user]);
    }

    /**
     * 确认兑换
     */
    public function actionDoExchange()
    {
        $points = (int) Yii::$app->request->post('points');
        $user_id = Yii::$app->request->post('user_id');
        if (null === ($user = OfflineUser::findOne($user_id))) {
            return ['code' => 1, 'message' => '找不到该用户'];
        }
        if ($user->points < $points) {
            return ['code' => 1, 'message' => '积分不足'];
        }
        $transaction = Yii::$app->db->beginTransaction();
        $pointOrder = $this->initPointOrder();
        $pointOrder->offGoodsName = trim(Yii::$app->request->post('offGoodsName'));
        $pointOrder->orderTime = trim(Yii::$app->request->post('orderTime'));
        $pointOrder->user_id = $user->id;
        $pointOrder->points = $points;
        try {
            $pointOrder->save();
            $pointManager = new OfflinePointManager();
            $pointManager->updatePoints($pointOrder, PointRecord::TYPE_OFFLINE_POINT_ORDER);
            $transaction->commit();
            return ['code' => 0, 'message' => '兑换成功'];
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return ['code' => 1, 'message' => $ex->getMessage()];
        }
    }

    /**
     * 初始化PointOrder的model
     */
    private function initPointOrder()
    {
        $model = new PointOrder();
        $model->sn = TxUtils::generateSn('OFF');
        $model->isPaid = true;
        $model->orderNum = null;
        $model->status = PointOrder::STATUS_SUCCESS;
        $model->type = PointRecord::TYPE_OFFLINE_POINT_ORDER;
        $model->isOffline = true;

        return $model;
    }
}
