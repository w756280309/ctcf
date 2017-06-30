<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\product\OnlineProduct;
use common\utils\StringUtils;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class CouponController extends BaseController
{
    /**
     * 我的代金券.
     */
    public function actionList($page = 1)
    {
        $c = CouponType::tableName();
        $uc = UserCoupon::tableName();

        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(['user_id' => $this->getAuthedUser()->id, 'isDisabled' => 0]);

        $count = $query->count();
        $pages = new Pagination(['totalCount' => $count, 'pageSize' => '10']);
        $model = $query
            ->select("$c.loanExpires, $c.amount, $c.name, $c.minInvest, if($uc.isUsed, bin(0),
             $uc.expiryDate < date(now())) as isExpired, $uc.expiryDate, $uc.isUsed, $uc.couponType_id")
            ->offset($pages->offset)
            ->limit($pages->limit)
            ->orderBy("isExpired, isUsed, $uc.expiryDate, amount desc, minInvest")
            ->asArray()
            ->all();

        foreach ($model as $key => $val) {
            $model[$key]['minInvestDesc'] = StringUtils::amountFormat1('{amount}{unit}', $val['minInvest']);
        }

        $tp = $pages->getPageCount();
        $code = ($page > $tp) ? 1 : 0;
        if (\Yii::$app->request->isAjax) {
            $message = ($page > $tp) ? '数据错误' : '消息返回';

            return [
                'header' => $pages,
                'data' => $model,
                'code' => $code,
                'message' => $message,
                'tp' => $tp,
                'cp' => $page,
            ];
        }

        return $this->render('list', ['model' => $model, 'header' => $pages]);
    }

    /**
     * 可用代金券列表.
     */
    public function actionValid($page = 1, $size = 10)
    {
        $request = $this->validateParams(Yii::$app->request->get());
        $loan = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $validCoupons = UserCoupon::fetchValid($this->getAuthedUser(), null, $loan);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $validCoupons,
            'pagination' => [
                'pageSize' => $size,
            ],
        ]);

        $pages = new Pagination(['totalCount' => $dataProvider->totalCount, 'pageSize' => $size]);
        $coupons = $dataProvider->models;

        $tp = $pages->pageCount;
        $code = ($page > $tp) ? 1 : 0;
        $message = ($page > $tp) ? '数据错误' : '消息返回';

        $header = [
            'count' => $pages->totalCount,
            'size' => $pages->pageSize,
            'tp' => $tp,
            'cp' => $pages->page + 1,
        ];

        $selectedCoupon = [];
        $key = 'loan_'.$request['sn'].'_coupon';
        $couponCount = 0;
        $couponMoney = 0;

        if (Yii::$app->session->has($key)) {
            $session = Yii::$app->session->get($key);
            $selectedCoupon = $session['couponId'];

            foreach ($selectedCoupon as $couponId) {
                $coupon = UserCoupon::findOne($couponId);
                if (is_null($coupon) || is_null($coupon->couponType)) {
                    continue;
                }
                $couponCount++;
                $couponMoney = bcadd($couponMoney, $coupon->couponType->amount, 2);
            }
        }

        $backArr = [
            'coupons' => $coupons,
            'sn' => $request['sn'],
            'money' => $request['money'],
            'selectedCoupon' => $selectedCoupon,
        ];

        Yii::$app->session->setFlash('order_money', $request['money']);

        if (Yii::$app->request->isAjax) {
            $this->layout = false;
            $html = $this->render('_valid_list', $backArr);

            return [
                'header' => $header,
                'html' => $html,
                'code' => $code,
                'message' => $message,
            ];
        }

        $this->layout = '@app/views/layouts/fe';

        return $this->render('valid_list', array_merge($backArr, [
            'header' => $header,
            'couponCount' => $couponCount,
            'couponMoney' => $couponMoney,
        ]));
    }

    /**
     * 根据输入的金额自动获取代金券.
     */
    public function actionValidForLoan()
    {
        $coupon = null;
        $request = $this->validateParams(Yii::$app->request->get());
        $loan = $this->findOr404(OnlineProduct::class, ['sn' => $request['sn']]);

        $coupons = UserCoupon::fetchValid($this->getAuthedUser(), $request['money'], $loan);
        $userCouponId = [];

        if ($coupons) {
            $coupon = reset($coupons);
            $userCouponId[] = $coupon->id;
        }

        Yii::$app->session->set('loan_'.$request['sn'].'_coupon', ['couponId' => $userCouponId]);

        $this->layout = false;

        return $this->render('_valid_coupon', ['coupons' => $coupon ? [['amount' => $coupon->couponType->amount]] : []]);
    }

    /**
     * 将对应的代金券ID存入session当中.
     */
    public function actionAddCouponSession($sn, $couponId, $money, $opt)
    {
        $user = $this->getAuthedUser();
        if (null === $user || !$this->validateLoanWithCoupon($sn, $couponId) || !in_array($opt, ['canceled', 'selected'])) {
            return $this->msg400(1, '参数错误');
        }
        $key = 'loan_'.$sn.'_coupon';
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

            Yii::$app->session->set('loan_'.$sn.'_coupon', ['couponId' => $couponIds]);

            return $this->msg200('勾选成功', $data);
        } catch (\Exception $ex) {
            $message = $ex->getMessage();

            if (1 === $ex->getCode()) {
                $message = '选第'.$couponCount.'张代金券需要投资额满'.StringUtils::amountFormat2($totalMinInvest).'元';
            }

            return $this->msg400(1, $message);
        }
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

    /**
     * 清空代金券操作.
     */
    public function actionDelCoupon($sn)
    {
        if ($this->validateLoanWithCoupon($sn, 0)) {
            Yii::$app->session->set('loan_'.$sn.'_coupon', ['couponId' => []]);
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

    private function validateParams($get)
    {
        $request = array_replace([
            'sn' => null,
            'money' => null,
        ], $get);

        if (empty($request['sn']) || !preg_match('/^[A-Za-z0-9]+$/', $request['sn'])) {
            throw $this->ex404();
        }

        if (empty($request['money']) || !preg_match('/^[0-9|.]+$/', $request['money'])) {
            $request['money'] = null;
        }

        return $request;
    }
}
