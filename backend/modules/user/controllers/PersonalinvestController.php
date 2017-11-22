<?php
namespace backend\modules\user\controllers;
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-11-14
 * Time: 上午11:25
 */
use backend\controllers\BaseController;
use common\lib\user\UserStats;
use common\models\offline\OfflineUser;
use common\models\order\OnlineRepaymentPlan;;
use common\models\user\User;
use common\utils\SecurityUtils;

class PersonalinvestController extends BaseController
{
    public function actionIndex()
    {
        $idcard = \Yii::$app->request->get('number');
        $type = \Yii::$app->request->get('type');
        if ($idcard && $type) {
            if ($type == 'online') {
                $model = User::findOne(['safeMobile' => SecurityUtils::encrypt($idcard)]);
            } else {
                $model = OfflineUser::findOne(['idCard' => $idcard]);
            }
        }
        return $this->render('index', ['model' => $model]);
    }

    //导出个人投资详情导出
    public function actionExport($number, $type)
    {
        if ($number) {
            $data = [];
            $data[] = [
                '客户姓名',
                '产品名称',
                '产品期限',
                '开户行名称',
                '银行卡号',
                '认购日',
                '起息日',
                '认购金额',
                '收益率',
                '付息时间',
                '付息金额',
                '到期日',
                '还款方式',
                '线上/线下'
                ];
            if ($type == 'online') {
                $exportdata = self::online($data, $number);
            } else {
                $exportdata = self::offline($data, $number);
            }

            if ($exportdata) {
                UserStats::exportAsXlsx($exportdata, '个人投资详情导出-'.$number.'.xlsx');
            }
        }
    }

    //线上用户数据
    public static function online($data, $number)
    {
        //线上
        $query = 'select u.id,u.real_name,b.bank_name,b.card_number from user as u inner join user_bank as b on u.id = b.uid where u.safeMobile = "'.SecurityUtils::encrypt($number).'"';
        $onlineUser = \Yii::$app->db->createCommand($query)->queryOne();
        if ($onlineUser['id']) {
            $query_order = 'select o.uid,o.id as oid,o.order_time,o.order_money,o.yield_rate,p.id as pid,p.finish_date,p.title,p.jixi_time,p.expires,p.refund_method from online_order as o inner join online_product as p on o.online_pid = p.id where o.uid= "'.$onlineUser['id'].'"';
            $onlineOrder = \Yii::$app->db->createCommand($query_order)->queryAll();
            if (count($onlineOrder) > 0) {
                foreach ($onlineOrder as $v) {
                    $fx_time = [];
                    $fx_money = [];
                    if ($v['jixi_time']) {
                        $repayment = OnlineRepaymentPlan::find()->where(['online_pid' => $v['pid'], 'order_id' => $v['oid'], 'uid' => $v['uid']])->orderBy('qishu')->all();
                        foreach($repayment as $val) {
                            $fx_time[] = substr(date("Y-m-d H:i", $val->refund_time), 0, 10);
                            $fx_money[] = $val->benxi;
                        }
                    }
                    if ($v['refund_method'] == 1) {
                        $method = '天';
                    } else {
                        $method = '个月';
                    }

                    $data[] = [
                        $onlineUser['real_name'],
                        $v['title'],
                        $v['expires'].$method,
                        $onlineUser['bank_name'],
                        $onlineUser['card_number'],
                        date("Y-m-d", $v['order_time']),
                        date("Y-m-d", $v['jixi_time']),
                        $v['order_money'],
                        round($v['yield_rate'], 3),
                        isset($fx_time) ? implode(',' , $fx_time) : '',
                        isset($fx_money) ? implode(',' , $fx_money) : '',
                        date("Y-m-d", $v['finish_date']),
                        \Yii::$app->params['refund_method'][$v['refund_method']],
                        '线上',
                    ];
                }
            }
        }
        return $data;
    }
    //线下
    public static function offline($data, $number)
    {
        $offlineUser = OfflineUser::findOne(['idCard' => $number]);
        if (!is_null($offlineUser)) {
            $query_order2 = "select o.id as oid,o.apr,o.money,o.orderDate,o.accBankName,o.bankCardNo,l.id as lid,l.title,l.expires,l.unit,l.yield_rate,l.finish_date,l.repaymentMethod,l.jixi_time from offline_order as o inner join offline_loan as l on o.loan_id = l.id where o.user_id = ". $offlineUser->id;
            $offlineOrder =  \Yii::$app->db->createCommand($query_order2)->queryAll();
            if (count($offlineOrder) > 0) {
                foreach ($offlineOrder as $v) {
                    $data[] = [
                        $offlineUser->realName,
                        $v['title'],
                        $v['expires'] . $v['unit'],
                        $v['accBankName'],
                        $v['bankCardNo'],
                        $v['orderDate'],
                        substr($v['jixi_time'], 0, 10),
                        $v['money'].'万',
                        round($v['apr'], 3),
                        '',
                        '',
                        substr($v['finish_date'], 0, 10),
                        \Yii::$app->params['refund_method'][$v['repaymentMethod']],
                        '线下'
                    ];
                }
            }
        }
        return $data;
    }
}