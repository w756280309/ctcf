<?php

namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\models\bank\BankCardUpdate;
use yii\data\ActiveDataProvider;

class BankController extends BaseController
{
    /**
     * 统计超过3天, 状态为处理中的换卡记录的条数.
     */
    public function actionCountForUpdate()
    {
        $count = $this->findBankcardUpdate()->count();

        return intval($count);
    }

    /**
     * 获取超过3天, 状态为处理中的换卡记录列表.
     */
    public function actionUpdateList()
    {
        $query = $this->findBankcardUpdate()
            ->innerJoinWith('user')
            ->orderBy(['created_at' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->sort = false;

        return $this->render('update_list', ['dataProvider' => $dataProvider]);
    }

    private function findBankcardUpdate()
    {
        $b = BankCardUpdate::tableName();

        return BankCardUpdate::find()
            ->where(["$b.status" => BankCardUpdate::STATUS_ACCEPT])
            ->andWhere(["<", "$b.created_at", strtotime("today - 3 days")]);
    }
}
