<?php

namespace wap\modules\promotion\controllers;

use common\controllers\HelpersTrait;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\promo\TicketToken;
use Yii;
use yii\web\Controller;

class P698Controller extends Controller
{
    public $layout = '@app/views/layouts/fe';
    use HelpersTrait;

    /**
     * 初始化页面
     */
    public function actionIndex()
    {
        $flags = [
            'c1' => false,
            'c2' => false,
        ];

        $couponSns = $this->getCouponSns();
        $user = $this->getAuthedUser();
        $couponTypes = CouponType::find()
            ->where(['in', 'sn', $couponSns])
            ->indexBy('sn')
            ->all();
        if (null !== $user) {
            foreach ($couponSns as $k => $sn) {
                $flags[$k] = null !== UserCoupon::find()
                    ->where(['user_id' => $user->id])
                    ->andWhere(['couponType_id' => $couponTypes[$sn]->id])
                    ->one();

            }
        }

        return $this->render('index', [
            'data' => $couponTypes,
            'flags' => $flags,
        ]);
    }

    /**
     * 领取页面
     */
    public function actionPull()
    {
        $type = Yii::$app->request->get('type');
        $user = $this->getAuthedUser();
        $couponSn = $this->getCouponSnByType($type);

        //判断参数是否正确
        if ('' === $couponSn) {
            return [
                'code' => 1,
                'messsage' => '参数错误',
            ];
        }

        //判断用户是否登录
        if (null === $user) {
            return [
                'code' => 2,
                'messsage' => '未登录',
            ];
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $couponType = CouponType::find()
                ->where(['sn' => $couponSn])
                ->one();
            if (null === $couponType) {
                throw new \Exception('未找到代金券');
            }
            $ticketKey = 'P698-' . $user->id . '-' . $couponType->id;
            TicketToken::initNew($ticketKey)->save(false);
            UserCoupon::addUserCoupon($user, $couponType)->save(false);
            $transaction->commit();
        } catch (\yii\db\IntegrityException $e) {
            if (23000 === (int) $e->getCode()) {
                return [
                    'code' => 3,
                    'messsage' => '已领取',
                ];
            }
            $transaction->rollBack();
            throw new $e;
        } catch (\Exception $ex) {
            $transaction->rollback();
            return [
                'code' => 4,
                'messsage' => '网络异常，请刷新后重试！',
            ];
        }

        return [
            'code' => 0,
            'message' => '领取成功',
        ];
    }

    private function getCouponSnByType($type)
    {
        $type = strval($type);
        $couponSns = $this->getCouponSns();

        return isset($couponSns[$type]) ? $couponSns[$type] : '';
    }

    private function getCouponSns()
    {
        return [
            'c1' => '0029:1000000-180',
            'c2' => '0030:2000000-230',
        ];
    }
}
