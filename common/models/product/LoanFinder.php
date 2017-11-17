<?php

namespace common\models\product;
use Yii;
use common\models\user\UserInfo;

class LoanFinder
{
    /**
     * 查找公开的标的（在PC/WAP理财列表页/WAP首页中有调用）
     *
     * @return \yii\db\ActiveQuery
     */
    public static function queryPublicLoans()
    {
        //用户理财余额
        $user = Yii::$app->user->getIdentity();
        $balance = 0;
        if (!is_null($user)) {
            $balance = $user->getJGMoney();
        }

        $query = OnlineProduct::find()
            ->select('*')
            ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
            ->addSelect(['isJiaxi' => 'if(jiaxi > 0 && status = 6, 1, 0)'])
            ->addSelect(['raiseDays' => 'if (status = 2, if(refund_method = 1, expires, expires * 30), 0)'])
            ->addSelect(['raiseSn' => 'if (status = 2, id, 0)'])
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE,
            ])
            ->andWhere(['<=', 'balance_limit', $balance]);

        if ($balance < 50000) {
            $query->andWhere('isLicai=0 or is_xs=1');
            $query->andWhere("NOT((cid = 2) and if(refund_method = 1, expires > 180, expires > 6))");
        }

        return $query;
    }
}
