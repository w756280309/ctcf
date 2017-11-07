<?php

namespace frontend\modules\user\controllers;

use common\models\product\OnlineProduct;
use common\models\coupon\UserCoupon;
use common\utils\StringUtils;
use frontend\controllers\BaseController;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class CouponController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 我的代金券.
     * 1. 代金券排序规则:
     *  a. 状态: 未使用,已使用,已过期;
     *  b. 截止日期由近到远;
     *  c. 同一状态,同一时间,面值降序;
     *  d. 同一状态,同一时间,最小使用额升序;
     */
    public function actionIndex()
    {
        $this->layout = 'main';
        $uc = UserCoupon::tableName();

        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $_query = clone $query;

        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => '10']);
        $model = $query
            ->select("$uc.*, if($uc.isUsed, bin(0), $uc.expiryDate < date(now())) as isExpired")
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy("isExpired, isUsed, $uc.expiryDate, amount desc, minInvest")
            ->all();

        $data = $_query->select("count(*) as count, sum(amount) as totalAmount")
            ->andWhere(['isUsed' => 0])
            ->andFilterWhere(['>=', 'expiryDate', date('Y-m-d')])
            ->createCommand()
            ->queryone();

        return $this->render('index', ['model' => $model, 'pages' => $pages, 'data' => $data]);
    }

    /**
     * 根据标的SN及购买金额，获得PC端可用代金券列表（带是否选中状态）的HTML
     *
     * @param string $sn              标的sn
     * @param string $money           购买金额
     *
     * @return string
     */
    public function actionValid($sn, $money)
    {
        $loan = OnlineProduct::findOne(['sn' => $sn]);
        if (null === $loan) {
            return $this->renderFile('_valid_coupon.php', [
                'validCoupons' => [],
                'selectedCoupons' => [],
            ]);
        }
        if (!is_numeric($money)) {
            $money = 0;
        }
        $selectedCoupons = [];
        $selectedCouponIds = [];
        $user = $this->getAuthedUser();
        $detailData = Yii::$app->session->get('detail_data', []);
        if (!empty($detailData)) {
            $selectedCouponIds = isset($detailData[$sn]['couponId']) ? $detailData[$sn]['couponId'] : [];
        }
        $isActiveSelected = isset($detailData[$sn]['active_select_coupon']) ? $detailData[$sn]['active_select_coupon'] : false;
        $validCoupons = UserCoupon::fetchValid($user, null, $loan);
        if (!empty($validCoupons)) {
            $selectedCouponIds = is_array($selectedCouponIds) ? array_filter($selectedCouponIds) : [];
            if ($isActiveSelected && !empty($selectedCouponIds)) {
                $validCouponIds = array_keys($validCoupons);
                foreach ($selectedCouponIds as $k => $couponId) {
                    if (!in_array($couponId, $validCouponIds)) {
                        unset($selectedCouponIds[$k]);
                        continue;
                    }
                    try {
                        $userCoupon = $validCoupons[$couponId];
                        UserCoupon::checkAllowUse($userCoupon, $money, $user, $loan);
                    } catch (\Exception $ex) {
                            continue;
                    }
                    $money = bcsub($money, $userCoupon->couponType->minInvest, 2);
                    $selectedCoupons[] = $userCoupon;
                }
            } else {
                //根据 sn和money 推荐一张代金券
                if ($money > 0) {
                    foreach ($validCoupons as $userCoupon) {
                        if ($money >= $userCoupon->couponType->minInvest) {
                            $selectedCoupons[] = $userCoupon;
                            break;
                        }
                    }
                }
            }
        }
        $detailData[$sn]['couponId'] = ArrayHelper::getColumn($selectedCoupons, 'id');
        Yii::$app->session->set('detail_data', $detailData);

        $this->layout = false;
        return $this->render("_valid_coupon.php", [
            'validCoupons' => $validCoupons,
            'selectedCoupons' => $selectedCoupons,
        ]);
    }

    /**
     * 添加或取消代金券
     *
     * @param string $sn       标的SN
     * @param string $couponId 选择的代金券ID
     * @param string $money    金额
     * @param string $opt      操作  'canceled' or 'selected'
     *
     * @return array
     */
    public function actionAddCoupon($sn, $couponId, $money, $opt)
    {
        $user = $this->getAuthedUser();
        if (null === $user || !$this->validateLoanWithCoupon($sn, $couponId) || !in_array($opt, ['canceled', 'selected'])) {
            return $this->msg400(1, '参数错误');
        }

        $detailData = Yii::$app->session->get('detail_data', []);
        $loan = OnlineProduct::findOne(['sn' => $sn]);
        $isSelected = $opt === 'selected';
        $detailLoan = isset($detailData[$sn]) ? $detailData[$sn] : [];
        if (!empty($detailLoan)) {
            $couponIds = !empty($detailLoan['couponId']) ? $detailLoan['couponId'] : [];
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
                /**
                 * 加息券只可使用一张
                 * 加息券和代金券不可同时使用
                 */
                if ($couponCount > 0) {
                    $jiaxi_count = UserCoupon::find()
                        ->innerJoinWith('couponType')
                        ->where([
                            'isUsed' => false,
                            'isDisabled' => false,
                            'user_id' => $user->id,
                        ])
                        ->andWhere(['in', 'user_coupon.id', $couponIds])
                        ->andWhere(['coupon_type.type' => 1])
                        ->count();
                    if ($jiaxi_count > 1) {
                        throw new \Exception('加息券每次只可使用一张');
                    } else if($jiaxi_count == 1 && $couponCount > 1) {
                        throw new \Exception('加息券不可与代金券同时使用');
                    }
                }

                $totalMinInvest = bcadd($totalMinInvest, $coupon->couponType->minInvest, 2);
                UserCoupon::checkAllowUse($coupon, $checkMoney, $user, $loan);
                $couponMoney = bcadd($couponMoney, $coupon->couponType->amount, 2);
                $checkMoney = bcsub($checkMoney, $coupon->couponType->minInvest, 2);
            }

            $data = [
                'total' => count($couponIds),
                'money' => $couponMoney,
            ];

            $detailData[$sn]['couponId'] = $couponIds;
            $detailData[$sn]['active_select_coupon'] = true;
            Yii::$app->session->set('detail_data', $detailData);

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

