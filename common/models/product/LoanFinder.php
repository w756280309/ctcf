<?php

namespace common\models\product;

class LoanFinder
{
    public static function queryLoans()
    {
        return OnlineProduct::find()
            ->select('*')
            ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
            ->where([
                'isPrivate' => 0,
                'del_status' => OnlineProduct::STATUS_USE,
                'online_status' => OnlineProduct::STATUS_ONLINE
            ]);
    }
}
