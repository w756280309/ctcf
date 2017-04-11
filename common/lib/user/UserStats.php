<?php

namespace common\lib\user;

use common\models\affiliation\Affiliator;
use common\models\affiliation\UserAffiliation;
use common\models\mall\ThirdPartyConnect;
use common\models\user\User;
use common\models\user\UserBanks;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\user\UserAccount;
use common\models\user\UserInfo;
use common\utils\SecurityUtils;
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
                '性别',
                '生日',
                '年龄',
                '注册位置',
                '注册渠道',
                '首投时间',
                '当前积分',
                '会员等级',
                '兑吧ID',
            ],
        ];

        $model = User::find()
            ->joinWith('qpay')
            ->joinWith('lendAccount')
            ->joinWith('info')
            ->joinWith('thirdPartyConnect');
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

        foreach ($model as $key => $user) {
            $userId = $user->id;
            $data[$key]['id'] = intval($userId);
            $data[$key]['created_at'] = date('Y-m-d H:i:s', $user->created_at);
            $data[$key]['name'] = $user->real_name;
            $data[$key]['mobile'] = floatval(SecurityUtils::decrypt($user->safeMobile));
            $data[$key]['idcard'] = $user->idcard ? substr($user->idcard, 0, 14) . '****' : '';    //todo 隐藏身份证号信息,显示前14位
            $data[$key]['affiliation'] = isset($affiliation[$userId]) ? $affiliation[$userId]['name'] : '官网';
            $data[$key]['idcard_status'] = intval($user->idcard_status);
            $data[$key]['mianmiStatus'] = intval($user->mianmiStatus);
            $data[$key]['bid'] = isset($user->qpay);
            $data[$key]['available_balance'] = $user->lendAccount ? floatval($user->lendAccount->available_balance) : 0;
            $data[$key]['rtotalFund'] = isset($recharge[$userId]) ? floatval($recharge[$userId]['rtotalFund']) : 0;
            $data[$key]['rtotalNum'] = isset($recharge[$userId]) ? floatval($recharge[$userId]['rtotalNum']) : 0;
            $data[$key]['dtotalFund'] = isset($draw[$userId]) ? floatval($draw[$userId]['dtotalFund']) : 0;
            $data[$key]['dtotalNum'] = isset($draw[$userId]) ? floatval($draw[$userId]['dtotalNum']) : 0;
            $data[$key]['ototalFund'] = isset($order[$userId]) ? floatval($order[$userId]['ototalFund']) : 0;
            $data[$key]['ototalNum'] = isset($order[$userId]) ? floatval($order[$userId]['ototalNum']) : 0;
            $data[$key]['firstInvestAmount'] = $user->info ? floatval($user->info->firstInvestAmount) : 0;
            $data[$key]['investment_balance'] = $user->lendAccount ? floatval($user->lendAccount->investment_balance) : 0;
            $gender = $user->getGender();
            if ($gender === 'male') {
                $sex = '男性';
            } elseif ($gender === 'female') {
                $sex = '女性';
            } else {
                $sex = '---';
            }
            $data[$key]['sex'] = $sex;
            $data[$key]['birthday'] = $user->getBirthday();
            $data[$key]['age'] = $user->idcard ? $age = date('Y') - substr($user->idcard, 6, 4) : 0;
            $data[$key]['regContext'] = $user->regContext;
            if ($user->regFrom === 1) {
                $regFrom = 'wap注册';
            } elseif ($user->regFrom === 2) {
                $regFrom = '微信注册';
            } elseif ($user->regFrom === 3) {
                $regFrom = 'app注册';
            } elseif ($user->regFrom === 4) {
                $regFrom = 'pc注册';
            } else {
                $regFrom = '未知来源注册';
            }
            $data[$key]['regFrom'] = $regFrom;
            $data[$key]['firstInvestDate'] = $user->info ? $user->info->firstInvestDate : '';
            $data[$key]['points'] = intval($user->points);
            $data[$key]['level'] = $user->getLevel();
            $data[$key]['publicId'] = $user->thirdPartyConnect ? $user->thirdPartyConnect->publicId : '';
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