<?php

namespace common\models\user;

class UserSearch extends User
{
    public function attributes()
    {
        return [
            'name',
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
        ];
    }

    public function search($params)
    {
        $join_user_info = false;//判断是否已经关联 user_info 表
        $join_user_account = false;//判断是否已经关联 user_account 表
        $join_user_coupon = false;//判断是否已经关联 user_coupon 表
        $join_coupon_type = false;//判断是否已经关联 coupon_type 表
        $query = User::find();
        $this->setAttributes($params, false);
        $query->andFilterWhere([
            'user.type' => User::USER_TYPE_PERSONAL,
            'user.is_soft_deleted' => 0,
        ]);
        //过滤姓名
        $query->andFilterWhere(['like', 'user.real_name', trim($this->name)]);
        //过滤手机号
        $query->andFilterWhere(['like', 'user.mobile', trim($this->mobile)]);
        //过滤 未投资时长
        $noInvestDaysMin = intval(trim($this->noInvestDaysMin));
        $noInvestDaysMax = intval(trim($this->noInvestDaysMax));
        if ($noInvestDaysMin > 0 || $noInvestDaysMax > 0) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if ($noInvestDaysMin > 0) {
                $date = date('Y-m-d', strtotime('- ' . $noInvestDaysMin . ' day'));
                $query->andFilterWhere(['<=', 'user_info.lastInvestDate', $date]);
            }
            if ($noInvestDaysMax > 0){
                $date = date('Y-m-d', strtotime('- ' . $noInvestDaysMax . ' day'));
                $query->andFilterWhere(['>=', 'user_info.lastInvestDate', $date]);
            }
        }
        //过滤可用余额
        $balanceMin = floatval(trim($this->balanceMin));
        $balanceMax = floatval(trim($this->balanceMax));
        if ($balanceMin > 0 || $balanceMax > 0) {
            if (!$join_user_account) {
                $query->innerJoin('user_account', 'user.id = user_account.uid');
                $join_user_account = true;
            }
            if ($balanceMin > 0) {
                $query->andFilterWhere(['>=', 'user_account.available_balance', $balanceMin]);
            }
            if ($balanceMax > 0) {
                $query->andFilterWhere(['<=', 'user_account.available_balance', $balanceMax]);
            }

        }
        //过滤投资次数
        $investCountMin = intval(trim($this->investCountMin));
        $investCountMax = intval(trim($this->investCountMax));
        if ($investCountMin > 0 || $investCountMax > 0) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if ($investCountMin > 0) {
                $query->andFilterWhere(['>=', 'user_info.investCount', $investCountMin]);
            }
            if($investCountMax > 0) {
                $query->andFilterWhere(['<=', 'user_info.investCount', $investCountMax]);
            }
        }
        //过滤投资总额
        $investTotalMin = floatval(trim($this->investTotalMin));
        $investTotalMax = floatval(trim($this->investTotalMax));
        if ($investTotalMin > 0 || $investTotalMax > 0) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            if ($investTotalMin > 0) {
                $query->andFilterWhere(['>=', 'user_info.investTotal', $investTotalMin]);
            }
            if($investTotalMax > 0) {
                $query->andFilterWhere(['<=', 'user_info.investTotal', $investTotalMax]);
            }
        }
        //过滤注册时间
        $regTimeMin = strtotime(trim($this->regTimeMin));
        $regTimeMax = strtotime(trim($this->regTimeMax));
        if ($regTimeMin > 0) {
            $query->andFilterWhere(['>=', 'user.created_at', $regTimeMin]);
        }
        if ($regTimeMax > 0) {
            $query->andFilterWhere(['<=', 'user.created_at', $regTimeMax]);
        }
        //过滤注册来源
        $regContext = trim($this->regContext);
        if (!empty($regContext)) {
            $query->andWhere(['user.regContext' => $regContext === 'other' ? '' : $regContext]);
        }
        //过滤代金券
        $couponAmountMin = intval(trim($this->couponAmountMin));
        $couponAmountMax = intval(trim($this->couponAmountMax));
        if ($couponAmountMin > 0 || $couponAmountMax > 0) {
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
            if ($couponAmountMin > 0) {
                $query->andHaving(['>=', 'sum(coupon_type.amount)', $couponAmountMin]);
            }
            if($couponAmountMax > 0) {
                $query->andHaving(['<=', 'sum(coupon_type.amount)', $couponAmountMax]);
            }
        }

        $query->with('lendAccount');
        return $query;
    }

}