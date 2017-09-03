<?php

namespace backend\modules\adv\controllers;

use backend\controllers\BaseController;
use common\models\adminuser\AdminLog;
use common\models\promo\DuoBao;
use common\models\promo\PromoLotteryTicket;
use common\models\user\User;
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
        $query = RankingPromo::find()
            ->orderBy([
                'endTime' => SORT_DESC,
                'id' => SORT_DESC,
            ]);

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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                AdminLog::initNew($model)->save(false);

                return $this->redirect('index');
            }
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

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save(false)) {
                AdminLog::initNew($model)->save(false);

                return $this->redirect('index');
            }
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
        if (empty($promo)) {
            throw $this->ex404('数据未找到');
        }
        $query = $this->getAwardQuery($promo);

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
        $query = $this->getAwardQuery($promo);
        $data = $query->all();
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
                echo $value['rewardName'] . "\t,";
                echo ($value['name'] ?: "")."\t\n";
            }
        }
    }

    private function getAwardQuery($promo)
    {
        return (new Query())
            ->select([
                'u.real_name',
                'u.safeMobile',
                't.drawAt',
                't.rewardedAt',
                't.reward_id',
                'a.name',
                't.user_id',
                'r.name as rewardName'
            ])->from('promo_lottery_ticket AS t')
            ->innerJoin('user AS u', 't.user_id = u.id')
            ->innerJoin('reward As r', 't.reward_id = r.id')
            ->leftJoin('user_affiliation AS ua', 't.user_id = ua.user_id')
            ->leftJoin('affiliator AS a', 'ua.affiliator_id = a.id')
            ->where(['t.isDrawn' => true, 't.promo_id' => $promo->id])
            ->andWhere('t.reward_id is not null')
            ->orderBy(['t.created_at' => SORT_DESC]);
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

    /**
     * 0元夺宝增加参与活动人数
     * TODO 可删除，临时代码
     */
    public function actionAddFake()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'duobao0504']);
        $promoAtfr = new DuoBao($promo);
        ///判断活动时间,1未开始,2活动中,3已结束
        if ($promoAtfr->promoTime() != 2) {
            Yii::$app->session->setFlash('info', '不再活动时间范围内！');
        }
        //参与记录
        $p = PromoLotteryTicket::tableName();
        $u = User::tableName();
        $promoLottery = PromoLotteryTicket::find()
            ->leftJoin('user', "$p.user_id = $u.id")
            ->where(['promo_id' => $promo->id])
            ->limit(10)
            ->orderBy('created_at desc')
            ->all();
        if (Yii::$app->request->isPost) {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();
            try {
                //更新sequence
                $sequence = $promoAtfr->joinSequence();
                if ($sequence > $promoAtfr::TOTAL_JOINER_COUNT) {
                    throw new \Exception('参与人员已满额或者虚拟抽奖数达到上限');
                }
                //插入ticket
                $ticket = new PromoLotteryTicket();
                $ticket->user_id = '0';
                $ticket->source = 'fake';
                $ticket->promo_id = $promo->id;
                $ticket->joinSequence = $sequence;
                if ($ticket->save(false)) {
                    Yii::$app->session->setFlash('info', '成功增加1条记录');
                }
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                exit($e->getMessage());
            }
        }

        return $this->render('addfake', [
            'promoLottery' => $promoLottery,
        ]);
    }
}