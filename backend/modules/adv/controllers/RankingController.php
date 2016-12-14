<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class RankingController extends BaseController
{
    /**
     * 活动列表页.
     */
    public function actionIndex()
    {
        $query = RankingPromo::find()->orderBy(['endAt' => SORT_DESC, 'id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort(false);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 添加活动信息.
     */
    public function actionCreate()
    {
        $model = new RankingPromo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AdminLog::initNew($model)->save(false);

            return $this->redirect('index');
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * 更改活动信息.
     */
    public function actionUpdate($id)
    {
        $model = $this->findOr404(RankingPromo::class, $id);

        $model->startAt = date('Y-m-d H:i:s', $model->startAt);
        $model->endAt = date('Y-m-d H:i:s', $model->endAt);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            AdminLog::initNew($model)->save(false);

            return $this->redirect('index');
        }

        return $this->render('_form', [
            'model' => $model,
        ]);
    }

    /**
     * 删除活动.
     */
    public function actionDelete($id)
    {
        $model = $this->findOr404(RankingPromo::class, $id);

        if ($model->delete()) {
            AdminLog::initNew($model)->save(false);
        }

        return $this->redirect('index');
    }

    /**
     * 活动的获奖列表
     */
    public function actionAwardList($id)
    {
        $promo = RankingPromo::findOne($id);
        if (empty($promo) || empty($promo->promoClass) || !class_exists($promo->promoClass) || !method_exists($promo->promoClass, 'getAward')) {
            throw $this->ex404('数据未找到');
        }
        $query = (new Query())
            ->select(['u.real_name', 'u.mobile', 't.drawAt', 't.rewardedAt', 't.reward_id', 'a.name', 't.user_id'])
            ->from(' `promo_lottery_ticket` AS t')
            ->innerJoin('user AS u', 't.user_id = u.id')
            ->leftJoin('user_affiliation AS ua', 't.user_id = ua.user_id')
            ->leftJoin('affiliator AS a', 'ua.affiliator_id = a.id')
            ->where(['t.isDrawn' => true, 't.promo_id' => $promo->id])
            ->andWhere('t.reward_id is not null')
            ->orderBy(['t.created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('award_list', [
            'promo' => $promo,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 导出抽奖奖励列表
     * @param $id
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionExportAward($id)
    {
        $promo = $this->findOr404(RankingPromo::className(), $id);
        $class = $promo->promoClass;
        if (!method_exists($class, 'getAward')) {
            throw $this->ex404();
        }
        $data = (new Query())
            ->select(['u.real_name', 'u.mobile', 't.drawAt', 't.rewardedAt', 't.reward_id', 'a.name', 't.user_id'])
            ->from(' `promo_lottery_ticket` AS t')
            ->innerJoin('user AS u', 't.user_id = u.id')
            ->leftJoin('user_affiliation AS ua', 't.user_id = ua.user_id')
            ->leftJoin('affiliator AS a', 'ua.affiliator_id = a.id')
            ->where(['t.isDrawn' => true, 't.promo_id' => $promo->id])
            ->andWhere('t.reward_id is not null')
            ->orderBy(['t.created_at' => SORT_DESC])
            ->all();
        if (count($data) > 0) {
            header("Content-Type: text/csv; charset=utf-8");
            header('Content-Disposition: attachment; filename="award_list_' . time() . '.csv"');
            echo "\xEF\xBB\xBF";
            echo "姓名\t,手机号\t,抽奖时间\t,发奖时间\t,奖品\t,注册渠道\t\n";
            foreach ($data as $value) {
                echo $value['real_name']."\t,";
                echo $value['mobile']."\t,";
                echo date('Y-m-d H:i:s', $value['drawAt'])."\t,";
                echo ($value['rewardedAt'] ? date('Y-m-d H:i:s', $value['rewardedAt']) : "")."\t,";
                $award = $class::getAward($value['reward_id']);
                echo (($award && isset($award['name'])) ? $award['name'] : '') . "\t,";
                echo ($value['name'] ?: "")."\t\n";
            }
        }
    }

    /**
     * 活动上线,下线.
     */
    public function actionOnline($id)
    {
        $promo = $this->findOr404(RankingPromo::class, $id);
        $promo->isOnline = !$promo->isOnline;

        $code = $promo->save(false);

        if ($code) {
            AdminLog::initNew($promo)->save(false);
        }

        return [
            'code' => $code ? 0 : 1,
            'message' => $code ? '操作成功' : '操作失败',
        ];
    }
}