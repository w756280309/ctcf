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
        //校验是否可用
        $coupon_check = UserCoupon::findOne($couponId);
        if (is_null($coupon_check) || is_null($coupon_check->couponType)) {
            throw new \Exception('未找到代金券！');
        }
        try {
            UserCoupon::checkAllowUse($coupon_check, $money, $user, $loan);
        } catch (\Exception $ex) {
            return $this->msg400('勾选失败', $ex->getMessage());
        }

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
                    $new_couponIds = [];
                    foreach ($couponIds as $k => $id) {
                        if ("$id" === "$couponId") {
                            unset($couponIds[$k]);
                        } else {
                            array_push($new_couponIds, $id);
                        }
                    }
                    $couponIds = $new_couponIds;
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
            $states = false; //代金券类型
            foreach ($couponIds as $couponId) {
                $coupon = UserCoupon::findOne($couponId);
                if (is_null($coupon) || is_null($coupon->couponType)) {
                    throw new \Exception('未找到代金券！');
                }
                /**
                 * 处理代金券和加息券
                 */
                if (!$states && $coupon->couponType->type == 1) {
                    $couponIds = array($couponId);
                    $states = $coupon->couponType->type;
                    continue;
                } else if ($states && $coupon->couponType->type == 0) {
                    $couponIds = array($couponId);
                    continue;
                }else if ($states == 1 && $coupon->couponType->type == 1) {
                    $couponIds = array($couponId);
                    continue;
                }
                $states = $coupon->couponType->type;

                $couponCount++;

                $totalMinInvest = bcadd($totalMinInvest, $coupon->couponType->minInvest, 2);
                UserCoupon::checkAllowUse($coupon, $checkMoney, $user, $loan);
                $couponMoney = bcadd($couponMoney, $coupon->couponType->amount, 2);
                $checkMoney = bcsub($checkMoney, $coupon->couponType->minInvest, 2);
            }
            $total = 0;
            foreach ($couponIds as $v) {
                $coupon_end = UserCoupon::findOne($v);
                if ($coupon_end->couponType->type) {
                    $total = $coupon_end->couponType->bonusRate;
                } else {
                    $total = bcadd($coupon_end->couponType->amount, $total, 2);
                }
            }

            $data = [
                'total' => count($couponIds),
                'money' => $total,
                'coupons' => $couponIds,
            ];
            Yii::$app->session->set('loan_coupon', ['rand' => $session['rand'], 'couponId' => $couponIds, 'money' => $money]);

            return $this->msg200('勾选成功', $data);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            $message = $ex->getMessage();

            if (1 === $code) {
                $message = '选第'.$couponCount.'张代金券或加息券需要投资额满'.StringUtils::amountFormat2($totalMinInvest).'元';
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
