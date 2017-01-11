<?php

namespace common\lib\user;

use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use common\models\user\UserInfo;
use common\utils\StringUtils;
use Wcg\Http\HeaderUtils;
use yii\helpers\ArrayHelper;

/**
 * 用户统计.
 */
class UserStats
{
    /**
     * 统计投资用户信息.
     */
    public static function collectLenderData($where = [])
    {
        $data = ['title' =>
            [
                '用户ID',
                '注册时间',
                '姓名',
                '联系方式',
                '身份证号',
                '分销商',
                '是否开户(1 开户 0 未开户)',
                '是否开通免密(1 开通 0 未开通)',
                '是否绑卡(1 绑卡 0 未绑卡)',
                '可用余额(元)',
                '充值成功金额(元)',
                '充值成功次数(次)',
                '提现成功金额(元)',
                '提现成功次数(次)',
                '投资成功金额(元)',
                '投资成功次数(次)',
                '首次购买金额(元)',
                '理财资产(元)',
            ],
        ];

        $u = User::tableName();
        $b = UserBanks::tableName();
        $a = UserAccount::tableName();
        $info = UserInfo::tableName();

        $model = (new \yii\db\Query)
            ->select("$u.*, $b.id as bid, $a.available_balance, $info.firstInvestAmount as firstInvestAmount, $a.investment_balance as investmentBalance")
            ->from($u)
            ->leftJoin($b, "$u.id = $b.uid")
            ->leftJoin($a, "$u.id = $a.uid")
            ->leftJoin($info, "$info.user_id = $u.id")
            ->where(["$u.type" => User::USER_TYPE_PERSONAL]);
        if (!empty($where)) {
            $model = $model->andWhere($where);
        }
        $model = $model->all();
        if (0 === count($model)) {
            return $data;
        }
        $userIds = ArrayHelper::getColumn($model, 'id');

        $recharge = RechargeRecord::find()
            ->select("sum(fund) as rtotalFund, count(id) as rtotalNum, uid")
            ->where(['status' => RechargeRecord::STATUS_YES])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->asArray()
            ->all();
        $recharge = ArrayHelper::index($recharge, 'uid');

        $draw = DrawRecord::find()
            ->select("sum(money) as dtotalFund, count(id) as dtotalNum, uid")
            ->where(['status' => [DrawRecord::STATUS_SUCCESS, DrawRecord::STATUS_EXAMINED]])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->asArray()
            ->all();
        $draw = ArrayHelper::index($draw, 'uid');

        $order = OnlineOrder::find()
            ->select("sum(order_money) as ototalFund, count(id) as ototalNum, uid")
            ->where(['status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->asArray()
            ->all();
        $order = ArrayHelper::index($order, 'uid');

        $affiliation = UserAffiliation::find()
            ->select(['user_id', 'affiliator.name'])
            ->leftJoin('affiliator', 'user_affiliation.affiliator_id = affiliator.id')
            ->where(['in', 'user_id', $userIds])
            ->asArray()
            ->all();
        $affiliation = ArrayHelper::index($affiliation, 'user_id');

        foreach ($model as $key => $val) {
            $data[$key]['id'] = $val['id'];
            $data[$key]['created_at'] = date('Y-m-d H:i:s', $val['created_at']);
            $data[$key]['name'] = $val['real_name'];
            $data[$key]['mobile'] = $val['mobile'];   //手机号后面加入tab键,防止excel表格打开时,显示为科学计数法
            $data[$key]['idcard'] = $val['idcard'] ? substr($val['idcard'], 0, 14) . '****' : '';    //隐藏身份证号信息,显示前14位
            if (isset($affiliation[$val['id']])) {
                $data[$key]['affiliation'] = $affiliation[$val['id']]['name'];
            } else {
                $data[$key]['affiliation'] = '官网';
            }

            $data[$key]['idcard_status'] = intval($val['idcard_status']);
            $data[$key]['mianmiStatus'] = intval($val['mianmiStatus']);

            if (null === $val['bid']) {
                $data[$key]['bid'] = 0;
            } else {
                $data[$key]['bid'] = 1;
            }

            $data[$key]['available_balance'] = floatval($val['available_balance']);

            if (isset($recharge[$val['id']])) {
                $data[$key]['rtotalFund'] = floatval($recharge[$val['id']]['rtotalFund']);
                $data[$key]['rtotalNum'] = floatval($recharge[$val['id']]['rtotalNum']);
            } else {
                $data[$key]['rtotalFund'] = 0;
                $data[$key]['rtotalNum'] = 0;
            }

            if(isset($draw[$val['id']])) {
                $data[$key]['dtotalFund'] = floatval($draw[$val['id']]['dtotalFund']);
                $data[$key]['dtotalNum'] = floatval($draw[$val['id']]['dtotalNum']);
            } else {
                $data[$key]['dtotalFund'] = 0;
                $data[$key]['dtotalNum'] = 0;
            }

            if (isset($order[$val['id']])) {
                $data[$key]['ototalFund'] = floatval($order[$val['id']]['ototalFund']);
                $data[$key]['ototalNum'] = floatval($order[$val['id']]['ototalNum']);
            } else {
                $data[$key]['ototalFund'] = 0;
                $data[$key]['ototalNum'] = 0;
            }

            $data[$key]['firstInvestAmount'] = floatval($val['firstInvestAmount']);
            $data[$key]['investmentBalance'] = floatval($val['investmentBalance']);
        }

        return $data;
    }

    /**
     * 生成csv导出文件
     */
    public static function createCsvFile(array $data)
    {
        if (empty($data)) {
            throw new \yii\web\NotFoundHttpException('The data is null');
        }

        $objPHPExcel = new \PHPExcel();
        $currentColumn = 1;
        foreach ($data as $row) {
            if (is_array($row)) {
                $currentCell = 'A';
                foreach ($row as $value) {
                    if (is_string($value)) {
                        $objPHPExcel->getActiveSheet()->setCellValueExplicit($currentCell.$currentColumn, $value);
                    } else {
                        $objPHPExcel->getActiveSheet()->setCellValue($currentCell.$currentColumn, $value);
                    }
                    ++$currentCell;
                }
            }
            ++$currentColumn;
        }

        header(HeaderUtils::getContentDispositionHeader('投资用户信息('.date('Y-m-d H:i:s').').xls', \Yii::$app->request->userAgent));

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit();
    }
}