<?php

namespace common\models\user;

class UserSearch extends User
{
    public function attributes()
    {
        return [
            'name',
            'mobile',
            'noInvestDays',
            'balance',
            'investCount',
            'investTotal',
            'regTime',
            'regContext',
            'couponAmount',
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
        $noInvestDays = intval(trim($this->noInvestDays));
        if ($noInvestDays > 0) {
            $date = date('Y-m-d', strtotime('- ' . $noInvestDays . ' day'));
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            $query->andFilterWhere(['<=', 'user_info.lastInvestDate', $date]);
        }
        //过滤可用余额
        $balance = floatval(trim($this->balance));
        if ($balance > 0) {
            if (!$join_user_account) {
                $query->innerJoin('user_account', 'user.id = user_account.uid');
                $join_user_account = true;
            }
            $query->andFilterWhere(['>=', 'user_account.available_balance', $balance]);
        }
        //过滤投资次数
        $investCount = intval(trim($this->investCount));
        if ($investCount > 0) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            $query->andFilterWhere(['>=', 'user_info.investCount', $investCount]);
        }
        //过滤投资总额
        $investTotal = floatval(trim($this->investTotal));
        if ($investTotal > 0) {
            if (!$join_user_info) {
                $query->leftJoin('user_info', 'user_info.user_id = user.id');
                $join_user_info = true;
            }
            $query->andFilterWhere(['>=', 'user_info.investTotal', $investTotal]);
        }
        //过滤注册时间
        $regTime = strtotime(trim($this->regTime));
        if ($regTime > 0) {
            $query->andFilterWhere(['>=', 'user.created_at', $regTime]);
        }
        //过滤注册来源
        $regContext = trim($this->regContext);
        if (!empty($regContext)) {
            $query->andFilterWhere(['user.regContext' => $regContext === 'other' ? '' : $regContext]);
        }
        //过滤代金券
        $couponAmount = intval(trim($this->couponAmount));
        if ($couponAmount > 0) {
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
            $query->having('sum(coupon_type.amount)');
        }

        $query->with('lendAccount');
        return $query;
    }

}