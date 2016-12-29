<?php

namespace console\controllers;

use common\models\user\User;
use common\models\user\DrawRecord;
use Ding\DingNotify;
use yii\console\Controller;

class DrawNotifyController extends Controller
{
    private $draw_count = [
        'small' => 3,
        'big'   => 5,
    ];   //提现次数

    /**
     * 提现超过指定次数提醒
     * 每天提醒一次，时间上午10点
     */
    public function actionReminder()
    {
        $u = User::tableName();
        $d = DrawRecord::tableName();
        $query = DrawRecord::find()
            ->select('count(*) as total')
            ->where(["date_format(from_unixtime($d.created_at), '%Y%m')" => date('Ym')])
            ->andWhere(["$u.type" => User::USER_TYPE_PERSONAL])
            ->innerJoin($u, "$u.id = $d.uid")
            ->groupBy('uid');

        $new_query = clone $query;
        $draw_count = $this->draw_count;
        $small_limit = (int) $query->having(['>=', 'total', $draw_count['small']])->count();
        $big_limit = (int) $new_query->having(['>=', 'total',  $draw_count['big']])->count();
        if ($small_limit > 0) {
            $notify = new DingNotify('wdjf_customer');
            $notify->charSentText('当月提现次数到达' . $draw_count['small'] . '次的用户有 【' . $small_limit . '】 人；提现次数到达'. $draw_count['big'] .'次的用户有 【' . $big_limit . '】 人！');
        }
    }
}
