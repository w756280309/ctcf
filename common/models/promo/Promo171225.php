<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;

class Promo171225 extends BasePromo
{
    private $orderAnnualLimit = 100000;

    /**
     * 获得奖池
     *
     * @param User      $user
     * @param \DateTime $joinTime
     *
     * @return array
     */
    public function getAwardPool(User $user, $joinTime)
    {
        $annualInvest = $this->getAnnualInvest($user);
        $grade = $this->getCurrentGrade($annualInvest);
        $drawnCountInGrade = $this->calcDrawnCountInCurrentGrade($user->id, $grade);
        $exceptSns = $this->getExceptRewardSns($user->id, $grade);
        $pool = $this->calcPool($grade, $drawnCountInGrade, $exceptSns);

        return $pool;
    }

    public function getAnnualInvest($user)
    {
        $startTime = new \DateTime($this->promo->startTime);
        $endTime = new \DateTime($this->promo->endTime);

        return UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $endTime->format('Y-m-d'));
    }

    private function getGradeAnnuals()
    {
        return [
            1 => [0, 50000],
            2 => [50000, 200000],
            3 => [200000, 500000],
            4 => [500000, 1000000],
            5 => [1000000, 9999999999],
        ];
    }

    private function hitRewardsForFrontend()
    {
        return [
            1 => [
                ['name' => '随机代金券', 'path' => 'wap/campaigns/active20171218/images/gift_15.png'],
                ['name' => '随机积分', 'path' => 'wap/campaigns/active20171218/images/gift_13.png'],
                ['name' => '维达抽纸', 'path' => 'wap/campaigns/active20171218/images/gift_08.png'],
            ],
            2 => [
                ['name' => '12.25元现金红包', 'path' => 'wap/campaigns/active20171218/images/gift_12.png'],
                ['name' => '7天3%加息券', 'path' => 'wap/campaigns/active20171218/images/gift_05.png'],
                ['name' => '意大利公鸡头皂', 'path' => 'wap/campaigns/active20171218/images/gift_04.png'],
            ],
            3 => [
                ['name' => '天堂伞', 'path' => 'wap/campaigns/active20171218/images/gift_07.png'],
                ['name' => 'Aquafresh三色牙膏', 'path' => 'wap/campaigns/active20171218/images/gift_11.png'],
                ['name' => '金龙鱼盘锦大米', 'path' => 'wap/campaigns/active20171218/images/gift_06.png'],
            ],
            4 => [
                ['name' => '50元超市卡', 'path' => 'wap/campaigns/active20171218/images/gift_14.png'],
                ['name' => '70元温都猫充值卡', 'path' => 'wap/campaigns/active20171218/images/gift_10.png'],
                ['name' => '小米体重秤', 'path' => 'wap/campaigns/active20171218/images/gift_03.png'],
            ],
            5 => [
                ['name' => '100元超市卡', 'path' => 'wap/campaigns/active20171218/images/gift_14.png'],
                ['name' => '175元温都猫充值卡', 'path' => 'wap/campaigns/active20171218/images/gift_10.png'],
                ['name' => '24英寸万向轮拉杆箱', 'path' => 'wap/campaigns/active20171218/images/gift_09.png'],
            ],
        ];
    }

    protected function calcDrawnCountInCurrentGrade($userId, $grade)
    {
        $awardList = Award::findByConfig($userId, $this->promo->id)
            ->select("award.*,reward.sn")
            ->orderBy(['award.id' => SORT_ASC])
            ->asArray()
            ->all();
        if (empty($awardList)) {
            $drawnCount = 0;
        } else {
            $drawnCount = 0;
            if ($grade > 1) {
                if (2 === $grade) {
                    $drawnCount = $this->getDrawnCountByAfterSn($awardList, 'C-R12.25');
                } elseif (3 === $grade) {
                    $drawnCount = $this->getDrawnCountByAfterSn($awardList, 'C-YG');
                } elseif (4 === $grade) {
                    $drawnCount = $this->getDrawnCountByAfterSn($awardList, 'C-CSK50');
                } elseif ($grade >= 5) {
                    $drawnCount = $this->getDrawnCountByAfterSn($awardList, 'C-CSK100');
                }
            } else {
                $drawnCount = count($awardList);
            }
        }

        return $drawnCount + 1;
    }

    private function getDrawnCountByAfterSn($awardList, $sn)
    {
        $drawnCount = 0;
        $tmpTime = null;
        $allCount = count($awardList);
        foreach ($awardList as $award) {
            if ($sn === $award['sn']) {
                break;
            }
            $drawnCount++;
        }

        return $allCount - $drawnCount;
    }

    /**
     * 获得要排除的奖品sn
     *
     * @param int $userId 用户ID
     * @param int $grade  级别
     *
     * @return array
     */
    protected function getExceptRewardSns($userId, $grade)
    {
        $exceptRewardSns = [];
        if (4 === $grade) {
            $exceptRewardSns = $this->getExceptRewardsBySns($userId, ['C-CZQ70', 'C-TZC']);
        } elseif (5 === $grade) {
            //先判断是否已获得了100元超市卡
            $award = Award::findByConfig($userId, $this->promo->id, ['C-CSK100'])->one();
            if (null !== $award) {
                $exceptRewardSns = $this->getExceptRewardsBySns($userId, ['C-CZQ175', 'C-LGX24', 'C-CSK50']);
            }
        }

        return $exceptRewardSns;
    }

    private function getExceptRewardsBySns($userId, $sns)
    {
        $exceptRewardSns = [];
        $award = Award::findByConfig($userId, $this->promo->id, $sns)->one();
        if (null !== $award) {
            $exceptRewardSns = $sns;
        }

        return $exceptRewardSns;
    }

    /**
     * @param int   $grade             级别
     * @param int   $drawnCountInGrade 次数
     * @param array $exceptRewards     待排除的奖品sn
     *
     * @return array
     * @throws \Exception
     */
    protected function calcPool($grade, $drawnCountInGrade, $exceptRewards = [])
    {
        $grade = (int) $grade;
        $drawnCountInGrade = (int) $drawnCountInGrade;
        //校验基础数据
        if ($grade < 1 && $grade > 5) {
            throw new \Exception('级别异常');
        }
        $originPool = $this->originGradePools();
        if (empty($originPool)) {
            throw new \Exception('奖池不能为空');
        }

        $pool = [];
        $originPool = $originPool[$grade];
        if ($grade >= 1) {
            //grade在[1，3）且本级别是第一次抽 或者 grade在[4,5]且本级别%10余1
            $firstDraw = ($grade <= 3 && 1 === $drawnCountInGrade) || (4 === $grade && 1 === $drawnCountInGrade % 10) || (5 === $grade && 1 === $drawnCountInGrade % 10);
            if ($firstDraw) {
                foreach ($originPool as $k=>$gv) {
                    $pool[$k] = $gv;
                    break;
                }
            } else {
                $pool = $originPool;
                if (!empty($exceptRewards)) {
                    foreach ($originPool as $sn => $reward) {
                        if (in_array($sn, $exceptRewards)) {
                            unset($originPool[$sn]);
                        }
                    }
                    $pool = $originPool;
                }
                if (5 === $grade) {
                    if (isset($pool['C-CSK100'])) {
                        unset($pool['C-CSK100']);
                    }
                }
            }
        }

        return $pool;
    }

    /**
     * 初始奖池配置
     */
    private function originGradePools()
    {
        return [
            1 => [
                'C-C5' => '0.3',
                'C-C15' => '0.3',
                'C-P8' => '0.2',
                'C-P12' => '0.15',
                'C-CZ' => '0.05',
            ],
            2 => [
                'C-R12.25' => '0.3',
                'C-BC7003' => '0.3',
                'C-GJTZ' => '0.05',
                'C-C15' => '0.3',
                'C-CZ' => '0.05',
            ],
            3 => [
                'C-YG' => '0.1',
                'C-TTS'=> '0.1',
                'C-DM' => '0.05',
                'C-R12.25' => '0.15',
                'C-P188' => '0.3',
                'C-BC7003' => '0.2',
                'C-CZ' => '0.1',
            ],
            4 => [
                'C-CSK50' => '0.01',
                'C-CZQ70' => '0.01',
                'C-TZC' => '0.01',
                'C-DM' => '0.07',
                'C-BC7003' => '0.1',
                'C-P188' => '0.3',
                'C-R12.25' => '0.3',
                'C-CZ' => '0.2',
            ],
            5 => [
                'C-CSK100' => '0.01',
                'C-CZQ175' => '0.01',
                'C-LGX24' => '0.01',
                'C-CSK50' => '0.07',
                'C-R12.25' => '0.2',
                'C-P188' => '0.2',
                'C-C15' => '0.2',
                'C-BC7003' => '0.1',
                'C-CZ' => '0.2',
            ],
        ];
    }

    /**
     * 根据此累计年化金额计算下一个累计年化金额
     *
     * @param string $annualInvest 累计年化金额
     *
     * @return string
     */
    public function getDeficiencyAnnual($grade, $annualInvest)
    {
        $annualInvest = floatval($annualInvest);
        $grade = (int) $grade;
        if (0 === $grade) {
            return 0;
        }
        if ($grade > 5) {
            $grade = 5;
        }
        $annualGrades = $this->getGradeAnnuals();
        $gradeAnnual = $annualGrades[$grade][0];
        if ($annualInvest > $gradeAnnual) {
            $deficiencyAnnual = $annualGrades[$grade][1] - $annualInvest + 100;
        } else {
            $deficiencyAnnual = $gradeAnnual - $annualInvest + 100;
        }

        return $deficiencyAnnual;
    }

    /**
     * 根据累计投资年化获得当前等级
     *
     * @param $annualInvest
     *
     * @return int
     */
    public function getCurrentGrade($annualInvest)
    {
        $grade = 0;
        $annualInvest = floatval($annualInvest);
        $annualGrades = $this->getGradeAnnuals();
        foreach ($annualGrades as $annualGrade => $annuals) {
            if ($annualInvest > $annuals[0] && $annualInvest <= $annuals[1]) {
                $grade = $annualGrade;
                break;
            }
        }

        return $grade;
    }

    /**
     * 获得礼包弹框显示的奖品弹窗信息
     *
     * @param int $grade 级别
     *
     * @return array
     * @throws \Exception
     */
    public function getHitRewardsByGrade($grade)
    {
        $grade = (int) $grade;
        if ($grade < 0 || $grade > 5) {
            throw new \Exception('当前所处级别异常');
        }

        $nextGrade = $grade + 1; //下个等级
        $hitRewardsForFrontend = $this->hitRewardsForFrontend(); //全部前台显示的各个等级必中奖品信息
        $data = [
            'currentPool' => isset($hitRewardsForFrontend[$grade]) ? $hitRewardsForFrontend[$grade] : [],
            'nextPool' => isset($hitRewardsForFrontend[$nextGrade]) ? $hitRewardsForFrontend[$nextGrade] : [],
        ];

        return $data;
    }

    /**
     * 根据订单添加抽奖机会
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        $ticket = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => 'any'])
            ->one();
        $endTime = new \DateTime($this->promo->endTime);
        if (null === $ticket) {
            PromoLotteryTicket::initNew($user, $this->promo, 'any', $endTime)->save();
        }
        $startDate = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $endDate = $endTime->format('Y-m-d');
        $annualInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
        $allNum = intval($annualInvestment / $this->orderAnnualLimit);
        $ticketCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => 'order'])
            ->count();
        $extraNum = max($allNum - $ticketCount, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoLotteryTicket::initNew($user, $this->promo, 'order', $endTime)->save();
        }
    }
}
