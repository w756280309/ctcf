<?php

namespace api\modules\tx\controllers;

use common\utils\SecurityUtils;
use common\models\tx\CreditOrder;
use common\models\tx\Order;
use common\models\user\User;
use yii\helpers\ArrayHelper;

class UserController extends Controller
{
    //获取用户的累计投资金额(以元为单位返回),投资金额只增不减
    public function actionTotalInvestment()
    {
        $userId = $this->request->query->getInt('id');
        $loanOrderAmount = Order::find()->where(['uid' => $userId, 'status' => 1])->sum('order_money');
        $creditOrderAmount = CreditOrder::find()->where(['user_id' => $userId, 'status' => CreditOrder::STATUS_SUCCESS])->sum('principal');
        return bcadd($loanOrderAmount, bcdiv($creditOrderAmount, 100, 2), 2);
    }

    /**
     * 获得线上累计投资金额的排行榜
     *
     * 以get方式请求该接口。
     *
     * @param  string $startDate 投资开始日期
     * @param  int    $limit     待获取条数，默认为5条
     *
     * @return array
     */
    public function actionTopList()
    {
        $limit = $this->request->query->getInt('limit', 5);
        $startDate = $this->request->query->get('startDate');
        if (strtotime($startDate) === false) {
            $startDate = '2016-04-19';
        }
        $orgUser = User::find()
            ->select('id')
            ->where(['type' => User::USER_TYPE_PERSONAL])
            ->andWhere('safeMobile is null')
            ->asArray()
            ->column();
        //获得转让与非转让的user_ids
        $userQuery = Order::find()
            ->select(['uid as user_id', 'sum(order_money) as totalInvest'])
            ->where(['status' => Order::STATUS_SUCCESS])
            ->andWhere(['>=', 'date(from_unixtime(created_at))', $startDate])
            ->andWhere(['not in', 'uid', $orgUser])
            ->groupBy('uid')
            ->orderBy(['totalInvest' => SORT_DESC]);

        $creditQuery = CreditOrder::find()
            ->select(['user_id', 'sum(principal / 100) as totalInvest'])
            ->where(['status' => CreditOrder::STATUS_SUCCESS])
            ->andWhere(['>=', 'createTime', $startDate])
            ->andWhere(['not in', 'user_id', $orgUser])
            ->groupBy('user_id')
            ->orderBy(['totalInvest' => SORT_DESC]);

        $newUserQuery = clone $userQuery;
        $newCreditQuery = clone $creditQuery;

        $users = $userQuery
            ->limit($limit)
            ->asArray()
            ->column();

        $creditUsers = $creditQuery
            ->limit($limit)
            ->asArray()
            ->column();

        $userIds = array_unique(ArrayHelper::merge($users, $creditUsers));

        $newUsers = $newUserQuery
            ->andWhere(['in', 'uid', $userIds])
            ->asArray()
            ->all();

        $newCreditUsers = $newCreditQuery
            ->andWhere(['in', 'user_id', $userIds])
            ->asArray()
            ->all();

        //合并两个数组并合并同一user_id的投资金额，然后投资金额降序排序取前5条
        $news = ArrayHelper::merge($newUsers, $newCreditUsers);
        ArrayHelper::multisort($news, 'user_id');
        $lastUserId = '';
        $newsArr = [];
        foreach ($news as $k => $user) {
            if ($lastUserId !== $user['user_id']) {
                $newsArr[$k] = $user;
                $lastUserId = $user['user_id'];
            } else {
                $newsArr[$k - 1]['totalInvest'] += $user['totalInvest'];
                continue;
            }
        }

        ArrayHelper::multisort($newsArr, 'totalInvest', SORT_DESC);
        $fiveArr = array_slice($newsArr, 0, 5);

        //把每个user数组的信息补上mobile
        $finalArr = [];
        foreach ($fiveArr as $k => $u) {
            $finalArr[$k] = $u;
            $finalArr[$k]['mobile'] = SecurityUtils::decrypt(User::find()->select('safeMobile')->where(['id' => $u['user_id']])->scalar());
        }

        return $finalArr;
    }
}
