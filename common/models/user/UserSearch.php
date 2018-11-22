<?php

namespace common\models\user;

use common\models\affiliation\UserAffiliation;
use common\utils\SecurityUtils;
use common\models\order\OnlineOrder;
use yii\helpers\ArrayHelper;

class UserSearch extends User
{
    public function attributes()
    {
        return [
            'name',
            'usercode',
            'status',
            'mobile',
            'noInvestDaysMin',
            'noInvestDaysMax',
            'balanceMin',
            'balanceMax',
            'investCountMin',
            'investCountMax',
            'investTotalMin',
            'investTotalMax',
            'regTimeMin',
            'regTimeMax',
            'regContext',
            'couponAmountMin',
            'couponAmountMax',
            'publicUserId',
            'investTimeMin',
            'investTimeMax',
            'isAffiliator',
        ];
    }

    public function search($params)
    {
        $join_user_info = false;//判断是否已经关联 user_info 表
        $join_user_account = false;//判断是否已经关联 user_account 表
        $join_user_coupon = false;//判断是否已经关联 user_coupon 表
        $join_coupon_type = false;//判断是否已经关联 coupon_type 表
        $join_third_party_connect = false; //是否关联 third_party_connect 表
        $uids = []; //最终需要筛选的user_id集合
        $get_user_ids = false;
        $query = User::find();
        $this->setAttributes($params, false);
        $query->andFilterWhere([
            'user.type' => User::USER_TYPE_PERSONAL,
            'user.is_soft_deleted' => 0,
        ]);
        //过滤姓名
        $query->andFilterWhere(['like', 'user.real_name', trim($this->name)]);
        //过滤手机号
        $query->andFilterWhere(['user.safeMobile'=>SecurityUtils::encrypt(trim($this->mobile))]);
        //过滤会员ID
        $query->andFilterWhere(['user.usercode' => trim($this->usercode)]);
        //过滤 未投资时长
        $noInvestDaysMin = is_numeric($this->noInvestDaysMin) ? intval(trim($this->noInvestDaysMin)) : null;
        $noInvestDaysMax = is_numeric($this->noInvestDaysMax) ? intval(trim($this->noInvestDaysMax)) : null;
        if (!is_null($noInvestDaysMax) || !is_null($noInvestDaysMin)) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if (!is_null($noInvestDaysMin)) {
                $date = date('Y-m-d', strtotime('- ' . $noInvestDaysMin . ' day'));
                $query->andWhere(['<=', 'user_info.lastInvestDate', $date]);
            }
            if (!is_null($noInvestDaysMax)) {
                $date = date('Y-m-d', strtotime('- ' . $noInvestDaysMax . ' day'));
                $query->andWhere(['>=', 'user_info.lastInvestDate', $date]);
            }
        }
        //过滤可用余额
        $balanceMin = is_numeric($this->balanceMin) ? floatval(trim($this->balanceMin)) : null;
        $balanceMax = is_numeric($this->balanceMax) ? floatval(trim($this->balanceMax)) : null;
        if (!is_null($balanceMin) || !is_null($balanceMax)) {
            if (!$join_user_account) {
                $query->innerJoin('user_account', 'user.id = user_account.uid');
                $join_user_account = true;
            }
            if (!is_null($balanceMin)) {
                $query->andWhere(['>=', 'user_account.available_balance', $balanceMin]);
            }
            if (!is_null($balanceMax)) {
                $query->andWhere(['<=', 'user_account.available_balance', $balanceMax]);
            }
        }
        //过滤投资次数
        $investCountMin = is_numeric($this->investCountMin) ? intval(trim($this->investCountMin)) : null;
        $investCountMax = is_numeric($this->investCountMax) ? intval(trim($this->investCountMax)) : null;
        if (!is_null($investCountMin) || !is_null($investCountMax)) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if (!is_null($investCountMin)) {
                $query->andWhere(['>=', 'user_info.investCount', $investCountMin]);
            }
            if(!is_null($investCountMax)) {
                $query->andWhere(['<=', 'user_info.investCount', $investCountMax]);
            }
        }
        //过滤投资总额
        $investTotalMin = is_numeric($this->investTotalMin) ? floatval(trim($this->investTotalMin)) : null;
        $investTotalMax = is_numeric($this->investTotalMax) ? floatval(trim($this->investTotalMax)) : null;
        if (!is_null($investTotalMin) || !is_null($investTotalMax)) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if (!is_null($investTotalMin)) {
                $query->andWhere(['>=', 'user_info.investTotal', $investTotalMin]);
            }
            if(!is_null($investTotalMax)) {
                $query->andWhere(['<=', 'user_info.investTotal', $investTotalMax]);
            }
        }
        //过滤注册时间
        if (!empty($this->regTimeMin) || !empty($this->regTimeMax)) {
            $regTimeMin = strtotime(trim($this->regTimeMin));
            $regTimeMax = strtotime(trim($this->regTimeMax) . ' 23:59:59');
            if ($regTimeMin) {
                $query->andFilterWhere(['>=', 'user.created_at', $regTimeMin]);
            }
            if ($regTimeMax) {
                $query->andFilterWhere(['<=', 'user.created_at', $regTimeMax]);
            }
        }
        //过滤注册来源
        $regContext = trim($this->regContext);
        if (!empty($regContext)) {
            $query->andWhere(['user.regContext' => $regContext === 'other' ? '' : $regContext]);
        }
        //过滤代金券
        $couponAmountMin = is_numeric($this->couponAmountMin) ? intval(trim($this->couponAmountMin)) : null;
        $couponAmountMax = is_numeric($this->couponAmountMax) ? intval(trim($this->couponAmountMax)) : null;
        if (!is_null($couponAmountMin) || !is_null($couponAmountMax)) {
            if (!$join_user_coupon) {
                $query->leftJoin('user_coupon', 'user_coupon.user_id = user.id');
                $join_user_coupon = true;
            }
            if (!$join_coupon_type) {
                $query->leftJoin('coupon_type', 'coupon_type.id = user_coupon.couponType_id');
                $join_coupon_type = true;
            }
            $query->andFilterWhere(['user_coupon.isUsed' => 1]);
            $query->groupBy('user.id');
            if (!is_null($couponAmountMin) ) {
                $query->andHaving(['>=', 'sum(coupon_type.amount)', $couponAmountMin]);
            }
            if(!is_null($couponAmountMax)) {
                $query->andHaving(['<=', 'sum(coupon_type.amount)', $couponAmountMax]);
            }
        }

        //兑吧用户ID
        $publicUserId = trim($this->publicUserId);
        if (!empty($publicUserId)) {
            if (!$join_third_party_connect) {
                $query->leftJoin('third_party_connect', 'user.id = third_party_connect.user_id');
                $join_third_party_connect = true;
            }
            $query->andWhere(['like', 'third_party_connect.publicId', $publicUserId]);
        }

        $query->with('lendAccount');

        //过滤分销商
        if (isset($params['affiliator_name']) && !empty($params['affiliator_name'])) {
            $aff = UserAffiliation::find()
                ->innerJoinWith('affiliator')
                ->where(['like', 'name', $params['affiliator_name']])
                ->select('user_id')
                ->all();

            $uids = ArrayHelper::getColumn($aff, 'user_id');
            $get_user_ids = true;
        }

        //过滤是否为被邀请人
        $isAffiliator = boolval($this->isAffiliator);
        if ($isAffiliator) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            $query->andWhere(['user_info.isAffiliator' => $isAffiliator]);
        }

        //过滤投资时间段（该时间段内有投资记录）
        if (!empty($this->investTimeMin) || !empty($this->investTimeMax)) {
            $investTimeMin = strtotime(trim($this->investTimeMin));
            $investTimeMax = strtotime(trim($this->investTimeMax) . ' 23:59:59');
            $queryOrder = OnlineOrder::find()->select('uid')->where(['status' => OnlineOrder::STATUS_SUCCESS]);
            if ($investTimeMin) {
                $queryOrder->andFilterWhere(['>=', 'created_at', $investTimeMin]);
            }
            if ($investTimeMax) {
                $queryOrder->andFilterWhere(['<=', 'created_at', $investTimeMax]);
            }
            $orderUsers = $queryOrder->groupBy('uid')->all();
            $orderUids = ArrayHelper::getColumn($orderUsers, 'uid');
            if (!empty($uids)) {
                if (!empty($orderUids)) {
                    $uids = array_intersect($uids, $orderUids);
                }
            } else {
                $uids = $orderUids;
            }
            $get_user_ids = true;
        }
        if ($get_user_ids) {
            $query->andWhere(['user.id' => $uids]);
        }

        //过滤会员状态
        is_numeric($this->status) && $query->andFilterWhere(['user.status'=>$this->status]);

        return $query;
    }
}
