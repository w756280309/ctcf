<?php

namespace wap\modules\mall\controllers;

use app\controllers\BaseController;
use common\models\code\Voucher;
use common\models\mall\PointRecord;
use Yii;

class PointController extends BaseController
{
    /**
     * 我的积分.
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'user' => $this->getAuthedUser(),
        ]);
    }

    /**
     * 积分规则.
     */
    public function actionRules()
    {
        return $this->render('rules');
    }

    /**
     * 积分明细.
     */
    public function actionList($page = 1, $size = 10)
    {
        $query = PointRecord::find()
            ->where(['user_id' => $this->getAuthedUser()->id])
            ->andWhere(['isOffline' => false])
            ->orderBy(['id' => SORT_DESC]);
        $pg = \Yii::$container->get('paginator')->paginate($query, $page, $size);
        $points = $query
            ->limit($pg->limit)
            ->offset($pg->offset)
            ->orderBy(['id' => SORT_DESC])
            ->all();
        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';
        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_list', ['points' => $points]);

            return ['header' => $pg->jsonSerialize(), 'html' => $html, 'code' => $code, 'message' => $message];
        }

        return $this->render('list', [
            'points' => $points,
            'header' => $pg->jsonSerialize(),
        ]);
    }

    /**
     * 兑换记录.
     *
     * 1. 每页显示十条记录;
     * 2. 排序按照先未领取状态后领取状态来排,后按照记录的创建时间的倒序来排;
     */
    public function actionPrizeList($page = 1, $size = 10)
    {
        $query = Voucher::find()
            ->joinWith('goodsType')
            ->where([
                'user_id' => $this->getAuthedUser()->id,
            ])->orderBy([
                'isRedeemed' => SORT_ASC,
                'createTime' => SORT_DESC,
            ]);

        $pg = Yii::$container->get('paginator')->paginate($query, $page, $size);
        $vouchers = $pg->getItems();

        $tp = $pg->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_prize_list', ['vouchers' => $vouchers]);

            return [
                'header' => $pg->jsonSerialize(),
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        $this->layout = '@app/views/layouts/fe';

        return $this->render('prize_list', [
            'vouchers' => $vouchers,
            'header' => $pg->jsonSerialize(),
        ]);
    }

    /**
     * 兑换代金券.
     */
    public function actionPrize($id)
    {
        $voucher = Voucher::findOne($id);

        try {
            $user = $this->getAuthedUser();
            if (
                is_null($voucher)
                || $user->id !== $voucher->user_id
                || $voucher->isRedeemed
            ) {
                throw new \Exception('兑换失败');
            }

            $voucher->redeemIp = Yii::$app->request->getUserIP();
            Voucher::redeem($voucher);

            return [
                'code' => 0,
                'message' => '兑换成功',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 1,
                'message' => $e->getMessage(),
            ];
        }
    }
}