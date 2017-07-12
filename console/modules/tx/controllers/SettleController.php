<?php

namespace console\modules\tx\controllers;

use Tx\UmpClient as Client;
use common\models\tx\DrawRecord;
use common\models\epay\EpayUser as FcUser;
use common\models\tx\RechargeRecord;
use common\models\tx\Settle;
use Yii;
use yii\console\Controller;

class SettleController extends Controller
{
    private $types = ['01', '02', '06']; //接口类型数组 01代表充值，02代表提现，06代表开户

    /**
     * 初始化从某一固定日期开始到某一天(null时为执行时间前一天)的对账数据，分段运行较好
     *
     * @param string $start 开始日期
     * @param string $end   截止日期，默认为null
     *
     * @throws \Exception
     */
    public function actionPrepare($start, $end = null)
    {
        if (null === $end) {
            $end = date('Y-m-d', strtotime('yesterday'));
        }

        $startTime = new \DateTime($start);
        $endTime = new \DateTime($end);

        if ($startTime > $endTime) {
            throw new \Exception('开始时间应小于等于结束时间');
        }

        $cloneStartTime = clone $startTime;
        $cloneEndTime = clone $endTime;

        //调用insertSettleWithFc,updateSettleWithLocal
        $fcNum = $this->insertSettleWithFc($startTime, $endTime);
        $lcNum = $this->updateSettleWithLocal($cloneStartTime, $cloneEndTime);

        var_dump([
            'fc' => $fcNum,
            'tx' => $lcNum,
        ]);
    }

    /**
     * 用于每日初始化联动数据
     */
    public function actionInitFc()
    {
        $yesterdayDate = new \DateTime('-1 day');
        echo $this->insertSettleWithFc($yesterdayDate, $yesterdayDate);
    }

    /**
     * 用于每日初始化本地数据
     */
    public function actionInitLocal()
    {
        $yesterdayDate = new \DateTime('-1 day');
        var_dump($this->updateSettleWithLocal($yesterdayDate, $yesterdayDate));
    }

    /**
     * 该方法用于指定时间内的对账，若不传参数，则默认对账前一日
     *
     * 考虑到提现到账日期为下一个工作日（T+1），节假日顺延，故不能以交易日期为查询条件对账。
     * 最终确定以上一个对账日期联动的对账单为准对账。
     * 对于联动漏单的情况应单独进行处理，即查询条件为对账日期为null,isChecked=false（详见FcLose方法）
     *
     * @param string $start 形如'2015-03-10',默认为null
     * @param string $end   形如'2015-03-10',默认为null
     *
     * @throws \Exception
     */
    public function actionCheck($start = null, $end = null)
    {
        $yesterdayDate = date('Y-m-d', strtotime('yesterday'));

        if (null === $start) {
            $start = $yesterdayDate;
        }

        if (null === $end) {
            $end = $yesterdayDate;
        }

        if ($start > $end) {
            throw new \Exception('开始日期应小于等于截止日期');
        }

        if ($start > $yesterdayDate || $end > $yesterdayDate) {
            throw new \Exception('开始日期或截止日期应小于当前日期');
        }

        $settles = Settle::find()
            ->where(['isChecked' => false])
            ->andWhere(['between', 'settleDate', $start, $end])
            ->all();

        foreach ($settles as $settle) {
            $settle->isChecked = true;
            if ($settle->fcAmount === $settle->amount && $settle->fcFee === $settle->fee && $settle->txDate === $settle->fcDate) {
                $settle->isSettled = true;
            }
            $bool = $settle->save();
            if (!$bool) {
                echo "数据库插入失败：{$settle->txSn}\n";
            }
        }

        $num = Settle::find()
            ->where(['settleDate' => $yesterdayDate])
            ->andWhere(['isSettled' => false])
            ->count();

        echo "对账日{$start}——{$end}内，有{$num}条对账失败";
    }

    /**
     * 查出当前时间以内联动漏掉的账单的类型及交易编号，输出为二维数组
     */
    public function actionFcLose()
    {
        $settles = Settle::find()->select('txSn, txType')->where(['settleDate' => null, 'isChecked' => false])->asArray()->all();
        var_dump($settles);
    }

    /**
     * @param \DateTime $start 开始时间
     * @param \DateTime $end   截止时间
     *
     * @return int $num
     *
     * @throws \Exception
     */
    private function insertSettleWithFc(\DateTime $start, \DateTime $end)
    {
        //调用对账接口，插入所有类型的对账数据
        $days = $end->diff($start)->days;

        if ($days > 30) {
            throw new \Exception('初始化联动对账数据的时间间隔应小于等于30天');
        }

        $num = 0;
        $db = Yii::$app->db_tx;
        $types = $this->types;
        $ump = Yii::$app->params['ump'];
        $httpClient = new Client($ump);

        for ($i = 0; $i <= $days; ++$i) {
            try {
                //对每天的联动的对账数据初始化到本地settle表里
                $transaction = $db->beginTransaction();
                $dateString = $start->format('Ymd');
                foreach ($types as $type) {
                    $content = $httpClient->getSettlement($dateString, $type);
                    $carr = explode('\n', $content);
                    foreach ($carr as $line) {
                        if (false !== stripos($line, 'start') or false !== stripos($line, 'end')) {
                            continue;
                        }
                        $settle = $this->initSettle($type, $line);
                        $settle->settleDate = $dateString;
                        if (!$settle->save()) {
                            throw new \Exception('插入失败：'.json_encode($settle->getAttributes()));
                        }
                        ++$num;
                        $settle = null;
                    }
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                var_dump($dateString.'日插入失败');
                echo $ex->getMessage();
                break;
            }
            $start->add(new \DateInterval('P1D'));
            if ($start > $end) {
                break;
            }
        }

        return $num; //返回插入成功的条数
    }

    /**
     * 该方法用于插入或更新指定时间段内的本地的对账数据
     *
     * @param \DateTime $start 开始时间
     * @param \DateTime $end   截止时间
     *
     * @return array $num 三种类型插入/更新成功的条数
     *
     * @throws \Exception
     */
    private function updateSettleWithLocal(\DateTime $start, \DateTime $end)
    {
        $days = $end->diff($start)->days;

        if ($days > 30) {
            throw new \Exception('初始化本地对账数据的时间间隔应小于等于30天');
        }

        $startDate = $start->format('Y-m-d');
        $endDate = $end->format('Y-m-d');

        $rechargeNum = $this->updateSettleWithRecharge($startDate, $endDate);
        $drawNum = $this->updateSettleWithDraw($startDate, $endDate);
        $fcUserNum = $this->updateSettleWithFcUser($startDate, $endDate);

        return [
            'rechargeNum' => $rechargeNum,
            'drawNum' => $drawNum,
            'fcUserNum' => $fcUserNum,
        ];
    }

    private function updateSettleWithRecharge($startDate, $endDate)
    {
        $rechargeNum = 0;
        $startUnixstamp = strtotime($startDate);
        $endUnixstamp = strtotime('+1 day', strtotime($endDate));
        $rechargeRecords = RechargeRecord::find()
            ->where(['status' => RechargeRecord::STATUS_YES])
            ->andWhere(['>=', 'created_at', $startUnixstamp])
            ->andWhere(['<', 'created_at', $endUnixstamp])
            ->all();

        foreach ($rechargeRecords as $rechargeRecord) {
            $newSettle = $this->initSettleWithLocal($rechargeRecord);
            if (!$newSettle->save()) {
                throw new \Exception('更新充值对账数据失败：'.json_encode($newSettle->getAttributes()));
            }
            ++$rechargeNum;
        }

        return $rechargeNum;
    }

    private function updateSettleWithDraw($startDate, $endDate)
    {
        $drawNum = 0;
        $startUnixstamp = strtotime($startDate);
        $endUnixstamp = strtotime('+1 day', strtotime($endDate));

        $drawRecords = DrawRecord::find()
            ->where(['status' => DrawRecord::STATUS_SUCCESS])
            ->andWhere(['>=', 'created_at', $startUnixstamp])
            ->andWhere(['<', 'created_at', $endUnixstamp])
            ->all();

        foreach ($drawRecords as $drawRecord) {
            $newSettle = $this->initSettleWithLocal($drawRecord);
            if (!$newSettle->save()) {
                throw new \Exception('更新提现对账数据失败：'.json_encode($newSettle->getAttributes()));
            }
            ++$drawNum;
        }

        return $drawNum;
    }

    private function updateSettleWithFcUser($startDate, $endDate)
    {
        $fcUserNum = 0;
        $fcUsers = FcUser::find()
            ->where(['>=', 'regDate', $startDate])
            ->andWhere(['<=', 'regDate', $endDate])
            ->all();

        foreach ($fcUsers as $fcUser) {
            $txSn = $fcUser->appUserId;
            $settle = Settle::findOne(['txSn' => $txSn]);
            $newSettle = $settle === null ? Settle::initNew() : $settle;
            $newSettle->txSn = $txSn;
            $newSettle->txType = Settle::PERS_REGISTER;
            $newSettle->txDate = $fcUser->regDate;
            if (!$newSettle->save()) {
                throw new \Exception('更新开通托管对账数据失败：'.json_encode($newSettle->getAttributes()));
            }
            ++$fcUserNum;
        }

        return $fcUserNum;
    }

    private function initSettleWithLocal($record)
    {
        $txSn = $record->sn;
        $settle = Settle::findOne(['txSn' => $txSn]);
        $newSettle = $settle === null ? Settle::initNew() : $settle;
        $newSettle->txSn = $txSn;
        $newSettle->amount = $record->amount;
        $newSettle->txType = $this->getTxtypeByLocal($record);
        $newSettle->fee = $record->getFee($newSettle->txType);
        $newSettle->txDate = $record->startDate;

        return $newSettle;
    }

    /**
     * 根据传入的充值/提现对象返回对应的交易类型编号
     *
     * @param object $record 产品对象
     *
     * @return int $txType 交易类型编号
     */
    private function getTxtypeByLocal($record)
    {
        if (in_array($record->user_id, [15, 53])) {
            $userType = 2;
        } else {
            $userType = 1;
        }

        if ($record instanceof DrawRecord) {
            if (1 === $userType) {
                $txType = Settle::PERS_DRAW;
            } elseif (2 === $userType) {
                $txType = Settle::CORP_DRAW;
            }
        } elseif ($record instanceof RechargeRecord) {
            $record->pay_type = (int) $record->pay_type;
            if (1 === $userType && $record->pay_type === 1) {
                $txType = Settle::PERS_QPAY_RECHARGE;
            } elseif (1 === $userType && $record->pay_type === 2) {
                $txType = Settle::PERS_EBANK_RECHARGE;
            } elseif (2 === $userType && $record->pay_type === 2) {
                $txType = Settle::CORP_EBANK_RECHARGE;
            }
        }

        return $txType;
    }

    /**
     * 根据对账单的产品号获得对账单的交易类型
     * 企业网银充值  P15110H0  1
     * 个人网银充值  P15F00G0  2
     * 个人快捷充值  P15600G0  3
     * 企业账户提现  P15U1000  4
     * 个人账户提现  P15T1000  5
     *
     * @param string $productNo 产品号
     *
     * @return int $txType 交易类型
     */
    private function getTxtypeByProductNo($productNo)
    {
        switch ($productNo) {
            case 'P15110H0':
                $txType = Settle::CORP_EBANK_RECHARGE;
                break;
            case 'P15F00G0':
                $txType = Settle::PERS_EBANK_RECHARGE;
                break;
            case 'P15600G0':
                $txType = Settle::PERS_QPAY_RECHARGE;
                break;
            case 'P15U1000':
                $txType = Settle::CORP_DRAW;
                break;
            case 'P15T1000':
                $txType = Settle::PERS_DRAW;
                break;
        }

        return $txType;
    }

    /**
     * 根据调用接口的类型及对账单返回的内容（一行），初始化一个settle对象
     *
     * @param string $type          接口类型
     * @param string $settleContent 对账单内容
     *
     * @return object Settle
     */
    private function initSettle($type, $settleContent)
    {
        //如果接口类型为06，则分隔符为“|”，否则为“，”
        if ('06' !== $type) {
            $delimiter = ',';
        } else {
            $delimiter = '|';
        }

        $items = explode($delimiter, $settleContent);
        $items = array_map(function ($item) {
            return trim($item);
        }, $items);
        switch ($type) {
            case '01';
                $txSn = $items[0];
                $fcDate = $items[1];
                $fcAmount = $items[4];
                $fcFee = $items[8];
                $fcSn = $items[7];
                $txType = $this->getTxtypeByProductNo($items[10]);
                break;
            case '02';
                $txSn = $items[2];
                $fcDate = $items[3];
                $fcAmount = $items[4];
                $fcFee = $items[5];
                $fcSn = $items[9];
                $txType = $this->getTxtypeByProductNo($items[11]);
                break;
            case '06';
                $txSn = $items[0]; //商户用户标识
                $fcDate = $items[7];
                $fcSn = $items[1]; //资金托管平台用户号
                $fcAmount = null;
                $fcFee = null;
                $txType = Settle::PERS_REGISTER;
                break;
        }

        $settle = Settle::findOne(['txSn' => $txSn]);
        $settle = null === $settle ? Settle::initNew() : $settle;
        $settle->txSn = $txSn;
        $settle->txType = $txType;
        $settle->fcDate = $fcDate;
        $settle->fcAmount = $fcAmount;
        $settle->fcFee = $fcFee;
        $settle->fcSn = $fcSn;

        return $settle;
    }
}
