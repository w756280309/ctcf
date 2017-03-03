<?php

namespace common\models\product;

class LoanFinder
{
    /**
     * 查找公开的标的（在PC/WAP理财列表页/WAP首页中有调用）
     *
     * @return \yii\db\ActiveQuery
     */
    public static function queryPublicLoans()
    {
        return OnlineProduct::find()
            ->select('*')
            ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
            ->addSelect(['isJiaxi' => 'if(jiaxi > 0 && status = 6, 1, 0)'])
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE
            ]);
    }
}
