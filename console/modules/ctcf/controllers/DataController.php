<?php

namespace console\modules\ctcf\controllers;

use common\models\user\UserAccount;
use common\models\user\UserInfo;
use common\utils\SecurityUtils;
use Yii;
use yii\console\Controller;

class DataController extends Controller
{
    public function actionMigrate()
    {
        $db = Yii::$app->db;
        $users = $db->createCommand('SELECT u.create_time, u.user_type, u.id,
                l.user_phone, l.password, l.login_time,
                c.real_name, c.id_card
                FROM t_user u
                left join t_login l on u.id=l.user_id
                left join t_safety_certification c on u.id=c.user_id')
            ->queryAll();

        foreach ($users as $u) {
            if (!preg_match('/^\d{11}$/', $u['user_phone']) || '1' != $u['user_type']) {
                continue;
            }

            $encryptedMobile = SecurityUtils::encrypt($u['user_phone']);

            $found = $db->createCommand("SELECT COUNT(id) FROM user WHERE safeMobile='".$encryptedMobile."'")
                ->queryScalar();
            if ($found > 0) {
                echo $u['user_phone']."\n";
                continue;
            }

            $x = [
                'type' => 1,
                'username' => 'ctcf:'.$u['id'],
                'usercode' => bin2hex(random_bytes(16)),
                'law_master_idcard' => $u['password'],
                'law_mobile' => '',
                'password_hash' => 'X',
                'auth_key' => Yii::$app->security->generateRandomString(),
                'status' => 1,
                'idcard_status' => 0,
                'invest_status' => 1,
                'mianmiStatus' => 0,
                'created_at' => strtotime($u['create_time']),
                'updated_at' => strtotime($u['create_time']),
                'regContext' => '',
                'points' => 0,
                //'annualInvestment' => 0.00,
                'safeMobile' => $encryptedMobile,
            ];

            $db->createCommand()->insert('user', $x)->execute();

            $uid = $db->getLastInsertId();

			$user_acount = new UserAccount();
            $user_acount->uid = $uid;
            $user_acount->type = UserAccount::TYPE_LEND;
            $user_acount->investment_balance = 5555.55;
            if (!$user_acount->save(false)) {
                echo $uid." account\n";
                break;
            }

            $userInfo = new UserInfo([
                'user_id' => $uid,
                'investCount' => 5555,
                'isAffiliator' => false,
            ]);
            if (!$userInfo->save(false)) {
                echo $uid." info\n";
                break;
            }
        }

        //var_dump($x);
    }

    public function actionInvest()
    {
        $db = Yii::$app->db;
        $loans = $db->createCommand('select b.id, b.borrow_title, b.borrow_rate, b.borrow_period, b.borrow_amount, b.min_invest_amount, b.invest_begin_time, b.borrow_status,
o.full_time, o.start_insterest, o.settle_time
from t_borrow b
left join t_borrow_operation o on b.id=o.borrow_id
where b.repayment_way=3 and b.is_test=2 and b.is_day=1')
            ->queryAll();
        // repayment_way = 3

        var_dump(count($loans));

        foreach ($loans as $loan) {
            if (!in_array($loan['borrow_status'], ['5', '6'])) {
                continue;
            }

            $x = [
                'epayLoanAccountId' => 'ctcf:'.$loan['id'],
                'title' => $loan['borrow_title'],
                'sn' => 'CTCF-LEGACY-'.$loan['id'],
                'cid' => 1,
                'is_xs' => 0,
                'recommendTime' => 0,
                'borrow_uid' => 0,
                'yield_rate' => $loan['borrow_rate']/100,
                'fee' => 0,
                'expires_show' => '',
                'refund_method' => 1,
                'expires' => $loan['borrow_period'],
                'kuanxianqi' => 0,
                'money' => $loan['borrow_amount'],
                'funded_money' => $loan['borrow_amount'],
                'start_money' => $loan['min_invest_amount'],
                'dizeng_money' => 1.00,
                'finish_date' => strtotime($loan['settle_time']),
                'start_date' => strtotime($loan['invest_begin_time']),
                'end_date' => strtotime($loan['start_insterest'])-1,
                'description' => '',
                'full_time' => strtotime($loan['full_time']),
                'jixi_time' => strtotime($loan['start_insterest']),
                'online_status' => 1,
                'yuqi_faxi' => 0,
                'allowTransfer' => 0,
                'isPrivate' => 0,
                'finish_rate' => 1,
                'is_jixi' => 1,
                'creator_id' => 1,
                'allowUseCoupon' => 0,
                'status' => $loan['borrow_status'],
                'isLicai' => 0,
                'sort' => '6' == $loan['borrow_status'] ? 70 : 60,
            ];

            $db->createCommand()->insert('online_product', $x)->execute();
        }
    }

    function actionOrder()
    {
        $db = Yii::$app->db;
        $orders = $db->createCommand('select i.id as order_id, i.amount, i.create_time, i.amount,
            p.id as loan_id, p.expires, p.yield_rate, p.status as loan_status,
            u.id as user_id
            from t_invest i
            inner join online_product p on concat("ctcf:", i.borrow_id)=p.epayLoanAccountId
            inner join user u on concat("ctcf:", i.user_id)=u.username
            where i.status=1')
            ->queryAll();
        // repayment_way = 3                                                                                           

        var_dump(count($orders));

	$tx = Yii::$app->db_tx;

        foreach ($orders as $order) {
            $x = [
		'sn' => 'ctcf:'.$order['order_id'],
                'online_pid' => $order['loan_id'],
                'refund_method' => 1,
                'yield_rate' => $order['yield_rate'],
                'expires' => $order['expires'],
                'order_money' => $order['amount'],
                'order_time' => strtotime($order['create_time']),
                'uid' => $order['user_id'],
                'username' => '',
                'status' => 1,
                'created_at' => strtotime($order['create_time']),
                'paymentAmount' => $order['amount'],
            ];

            $db->createCommand()->insert('online_order', $x)->execute();

            $orderId = $db->getLastInsertId();

	$y = [
		'user_id' => $order['user_id'],
		'loan_id' => $order['loan_id'],
		'order_id' => $orderId,
		'isRepaid' => '5' == $order['loan_status'] ? 0 : 1,
		'amount' => $order['amount']*100,
		'orderTime' => $order['create_time'],
		'createTime' => $order['create_time'],
		'maxTradableAmount' => $order['amount'],
		'isTrading' => 0,
		'isTest' => 0,
		'allowTransfer' => 0,
		'credit_order_id' => 5555,
	];

            $tx->createCommand()->insert('user_asset', $y)->execute();
        }
    }

	function actionInfo()
	{
	$db = Yii::$app->db;
	$infos = $db->createCommand('select uid, count(id) order_count, sum(order_money) order_sum, date(from_unixtime(min(created_at))) first_date, date(from_unixtime(max(created_at))) last_date
	from online_order where substr(sn, 1, 4)="ctcf" group by uid')
		    ->queryAll();

foreach ($infos as $info) {
	$x = [
		//'user_id' => $info['uid'],
		'isInvested' => $info['order_count'] > 0,
		'investCount' => $info['order_count'],
		'investTotal' => $info['order_sum'],
		'firstInvestDate' => $info['first_date'],
		'lastInvestDate' => $info['last_date'],
	];
var_dump($x);break;
	$db->createCommand()->update('user_info', $x, 'user_id = '.$info['uid'])->execute();
}
	}
}
