<?php

namespace common\lib\user;

use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\mall\ThirdPartyConnect;
use common\models\user\User;
use common\models\user\UserBank;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use common\models\user\UserInfo;
use common\service\UserService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Wcg\Http\HeaderUtils;
use yii\db\Query;
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
                '性别',
                '生日',
                '年龄',
                '注册位置',
                '注册渠道',
                '注册IP',
                '注册IP位置',
                '首投时间',
                '当前积分',
                '会员等级',
                '兑吧ID',
            ],
        ];

        $dbRead = \Yii::$app->db_read;
        $u = User::tableName();
        $ub = UserBank::tableName();
        $ua = UserAccount::tableName();
        $ui = UserInfo::tableName();
        $tpc = ThirdPartyConnect::tableName();
        $model = (new Query())
            ->select("$u.*,$ui.id as bid, $ua.available_balance, $ui.firstInvestAmount, $ua.investment_balance, $u.birthdate, $ui.firstInvestDate, $tpc.publicId")
            ->from($u)
            ->leftJoin($ub, "$ub.uid = $u.id")
            ->leftJoin($ua, "$ua.uid = $u.id")
            ->leftJoin($ui, "$ui.user_id = $u.id")
            ->leftJoin($tpc, "$tpc.user_id = $u.id")
            ->where(["$ua.type" => 1]);

        if (!empty($where)) {
            $model = $model->andWhere($where);
        }
        $model = $model->orderBy(["$u.id" => SORT_ASC])->all($dbRead);

        if (0 === count($model)) {
            return $data;
        }
        $userIds = ArrayHelper::getColumn($model, 'id');

        $recharge = (new Query())
            ->select("sum(fund) as rtotalFund, count(id) as rtotalNum, uid")
            ->from(RechargeRecord::tableName())
            ->where(['status' => RechargeRecord::STATUS_YES])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->all($dbRead);
        $recharge = ArrayHelper::index($recharge, 'uid');

        $draw = (new Query())
            ->select("sum(money) as dtotalFund, count(id) as dtotalNum, uid")
            ->from(DrawRecord::tableName())
            ->where(['status' => [DrawRecord::STATUS_SUCCESS, DrawRecord::STATUS_EXAMINED]])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->all($dbRead);
        $draw = ArrayHelper::index($draw, 'uid');

        $order = (new Query())
            ->select("sum(order_money) as ototalFund, count(id) as ototalNum, uid")
            ->from(OnlineOrder::tableName())
            ->where(['status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['in', 'uid', $userIds])
            ->groupBy("uid")
            ->all($dbRead);
        $order = ArrayHelper::index($order, 'uid');

        $affiliation = (new Query())
            ->select(['user_id', 'affiliator.name'])
            ->from(UserAffiliation::tableName())
            ->leftJoin('affiliator', 'user_affiliation.affiliator_id = affiliator.id')
            ->where(['in', 'user_id', $userIds])
            ->all($dbRead);
        $affiliation = ArrayHelper::index($affiliation, 'user_id');

        foreach ($model as $key => $user) {
            $userId = intval($user['id']);
            $data[$key]['id'] = $userId;
            $data[$key]['created_at'] = date('Y-m-d H:i:s', $user['created_at']);
            $data[$key]['name'] = $user['real_name'];
            $data[$key]['mobile'] = floatval(SecurityUtils::decrypt($user['safeMobile']));
            $idcard = SecurityUtils::decrypt($user['safeIdCard']);
            $data[$key]['idcard'] = $user['safeIdCard'] ? substr($idcard, 0, 14) . '****' : '';    //todo 隐藏身份证号信息,显示前14位
            $data[$key]['affiliation'] = isset($affiliation[$userId]) ? $affiliation[$userId]['name'] : '官网';
            $data[$key]['idcard_status'] = intval($user['idcard_status']);
            $data[$key]['mianmiStatus'] = intval($user['mianmiStatus']);
            $data[$key]['bid'] = $user['bid'] > 0 ? 1 : 0;
            $data[$key]['available_balance'] = floatval($user['available_balance']);
            $data[$key]['rtotalFund'] = isset($recharge[$userId]) ? floatval($recharge[$userId]['rtotalFund']) : 0;
            $data[$key]['rtotalNum'] = isset($recharge[$userId]) ? floatval($recharge[$userId]['rtotalNum']) : 0;
            $data[$key]['dtotalFund'] = isset($draw[$userId]) ? floatval($draw[$userId]['dtotalFund']) : 0;
            $data[$key]['dtotalNum'] = isset($draw[$userId]) ? floatval($draw[$userId]['dtotalNum']) : 0;
            $data[$key]['ototalFund'] = isset($order[$userId]) ? floatval($order[$userId]['ototalFund']) : 0;
            $data[$key]['ototalNum'] = isset($order[$userId]) ? floatval($order[$userId]['ototalNum']) : 0;
            $data[$key]['firstInvestAmount'] = floatval($user['firstInvestAmount']);
            $data[$key]['investment_balance'] = floatval($user['investment_balance']);
            $sex = '--';
            if ($idcard) {
                $sexNum = intval(substr($idcard, -2, 1));
                if ($sexNum % 2 === 1) {
                    $sex = '男性';
                } else {
                    $sex = '女性';
                }
            }
            $data[$key]['sex'] = $sex;
            $data[$key]['birthday'] = $user['birthdate'];
            $data[$key]['age'] = $user['birthdate'] ? $age = date('Y') - substr($user['birthdate'], 0, 4) : '--';
            $data[$key]['regContext'] = $user['regContext'];
            if ($user['regFrom'] === 1) {
                $regFrom = 'wap注册';
            } elseif ($user['regFrom'] === 2) {
                $regFrom = '微信注册';
            } elseif ($user['regFrom'] === 3) {
                $regFrom = 'app注册';
            } elseif ($user['regFrom'] === 4) {
                $regFrom = 'pc注册';
            } else {
                $regFrom = '未知来源注册';
            }
            $data[$key]['regFrom'] = $regFrom;
            $data[$key]['registerIp'] = $user['registerIp'];
            $data[$key]['regLocation'] = $user['regLocation'];
            $data[$key]['firstInvestDate'] = $user['firstInvestDate'];
            $data[$key]['points'] = intval($user['points']);
            $data[$key]['level'] = UserService::calcUserLevel(bcdiv($user['annualInvestment'], 10000, 0));
            $data[$key]['publicId'] = $user['publicId'];
        }

        return $data;
    }

    /**
     * 根据要到处的数据生成PHPExcel对象
     * @param $exportData         array   需要导出的数据，二维数组，包含标题，如果数据项是字符串类型，导出后单元格为文本格式
     * @return \PHPExcel
     * @throws \yii\web\NotFoundHttpException
     */
    public static function initPhpExcelObject(array  $exportData)
    {
        if (empty($exportData)) {
            throw new \yii\web\NotFoundHttpException('The data is null');
        }

        $objPHPExcel = new \PHPExcel();
        $currentColumn = 1;
        $currentCell = 'A';
        foreach ($exportData as $row) {
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
        foreach (range('A', $currentCell) as $columnId) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnId)->setAutoSize(true);
        }
        return $objPHPExcel;
    }

    /**
     * 导出成xlsx文件
     * @param $exportData         array   需要导出的数据，二维数组，包含标题，如果数据项是字符串类型，导出后单元格为文本格式
     * @param $fileName     string  导出的文件名字,扩展名为xlsx , 如 “投资用户信息.xlsx”
     */
    public static function exportAsXlsx(array $exportData, $fileName = '')
    {
        $objPHPExcel = self::initPhpExcelObject($exportData);
        if (empty($fileName)) {
            $fileName = '投资用户信息('.date('Y-m-d H:i:s').').xlsx';
        }
        header(HeaderUtils::getContentDispositionHeader($fileName, \Yii::$app->request->userAgent));
        ob_clean();
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit();
    }
}