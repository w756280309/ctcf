<?php

namespace common\action\user;

use common\controllers\HelpersTrait;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;
use yii\base\Action;

class AddCouponAction extends Action
{
    use HelpersTrait;

    /**
     * 添加或取消代金券 - todo 目前只有WAP端，以后会将PC端也合并在Action（目前为session字段不统一）
     *
     * @param string $sn       标的SN
     * @param string $couponId 选择的代金券ID
     * @param string $money    金额
     * @param string $opt      操作  'canceled' or 'selected'
     *
     * @return array
     */
    public function run($sn, $couponId, $money, $opt)
    {
        $user = $this->getAuthedUser();
        if (null === $user || !$this->validateLoanWithCoupon($sn, $couponId) || !in_array($opt, ['canceled', 'selected'])) {
            return $this->msg400(1, '参数错误');
        }

        $key = 'loan_coupon';
        $loan = OnlineProduct::findOne(['sn' => $sn]);
        $isSelected = $opt === 'selected';
        $hasSession = Yii::$app->session->has($key);
        if ($hasSession) {
            $session = Yii::$app->session->get($key);
            $couponIds = !empty($session['couponId']) ? $session['couponId'] : [];
            if ($isSelected) {
                if (!in_array($couponId, $couponIds)) {
                    array_push($couponIds, $couponId);
                }
            } else {
                if (!in_array($couponId, $couponIds)) {
                    return $this->msg400(1, '此代金券不在选中的代金券列表内');
                } else {
                    foreach ($couponIds as $k => $id) {
                        if ("$id" === "$couponId") {
                            unset($couponIds[$k]);
                        }
                    }
                }
            }
        } else {
            $couponIds = [];
            if ($isSelected) {
                if ($this->validateLoanWithCoupon($sn, $couponId)) {
                    $couponIds[] = $couponId;
                }
            } else {
                return $this->msg400(1, '无可勾选的代金券');
            }
        }

        $couponMoney = 0;
        $couponCount = 0;
        $totalMinInvest = 0;
        $checkMoney = $money;

        try {
            foreach ($couponIds as $couponId) {
                $coupon = UserCoupon::findOne($couponId);
                if (is_null($coupon) || is_null($coupon->couponType)) {
                    throw new \Exception('未找到代金券！');
                }
                $couponCount++;
                $totalMinInvest = bcadd($totalMinInvest, $coupon->couponType->minInvest, 2);
                UserCoupon::checkAllowUse($coupon, $checkMoney, $user, $loan);
                $couponMoney = bcadd($couponMoney, $coupon->couponType->amount, 2);
                $checkMoney = bcsub($checkMoney, $coupon->couponType->minInvest, 2);
            }

            $data = [
                'total' => count($couponIds),
                'money' => $couponMoney,
            ];
            Yii::$app->session->set('loan_coupon', ['couponId' => $couponIds]);

            return $this->msg200('勾选成功', $data);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            $message = $ex->getMessage();

            if (1 === $code) {
                $message = '选第'.$couponCount.'张代金券需要投资额满'.StringUtils::amountFormat2($totalMinInvest).'元';
            } else {
                $code = 2;
            }

            return $this->msg400($code, $message);
        }
    }


    private function validateLoanWithCoupon($sn, $couponId)
    {
        if (empty($sn) || !preg_match('/^[A-Za-z0-9]+$/', $sn)) {
            return false;
        }

        if (!empty($couponId) && !preg_match('/^[0-9]+$/', $couponId)) {
            return false;
        }

        return OnlineProduct::findOne(['sn' => $sn]);
    }

    private function msg400($code = 1, $msg = '操作失败', array $data = [])
    {
        Yii::$app->response->statusCode = 400;

        return [
            'code' => $code,
            'message' => $msg,
            'data' => $data,
        ];
    }

    private function msg200($msg = '操作成功', array $data = [])
    {
        return [
            'code' => 0,
            'message' => $msg,
            'data' => $data,
        ];
    }
}
