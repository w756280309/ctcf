<?php
namespace common\models\product;

use common\lib\product\ProductProcessor;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineOrder;
use common\models\user\MoneyRecord;
use common\models\user\User;
use P2pl\Borrower;
use P2pl\LoanInterface;
use Yii;
use yii\base\Exception;
use yii\behaviors\TimestampBehavior;

/**
 * 标的（项目）.
 *
 * @property string $id
 * @property string $title
 * @property string $sn
 * @property string $cid
 * @property int $borrow_uid
 * @property string $yield_rate
 * @property string $fee
 * @property string $expires_show
 * @property string $refund_method
 * @property int $expires
 * @property string $money
 * @property string $start_money
 * @property string $dizeng_money
 * @property int $start_date
 * @property int $end_date
 * @property string $description
 * @property int $full_time
 * @property int $del_status
 * @property int $status
 * @property string $yuqi_faxi
 * @property int $order_limit
 * @property string $creator_id
 * @property string $create_at
 * @property string $updated_at
 * @property string $isFlexRate
 * @property string $rateSteps
 * @property integer $paymentDay
 * @property int $isTest
 */
class OnlineProduct extends \yii\db\ActiveRecord implements LoanInterface
{
    public $is_fdate = 0;//是否启用截止日期

    //1预告期、 2募集中,3满标,4流标,5还款中,6已还清7募集提前结束  特对设立阀值得标的进行的设置。
    const STATUS_PRE = 1; //
    const STATUS_NOW = 2; //
    const STATUS_FULL = 3;
    const STATUS_LIU = 4;
    const STATUS_HUAN = 5;
    const STATUS_OVER = 6;
    const STATUS_FOUND = 7; //

    const STATUS_DEL = 1;
    const STATUS_USE = 0;

    const STATUS_ONLINE = 1;
    const STATUS_PREPARE = 0;

    const REFUND_METHOD_DAOQIBENXI = 1;//到期本息
    const REFUND_METHOD_MONTH = 2;//按月付息，到期本息
    const REFUND_METHOD_QUARTER = 3;//按季付息，到期本息
    const REFUND_METHOD_HALF_YEAR = 4;//按半年付息，到期本息
    const REFUND_METHOD_YEAR = 5;//按年付息，到期本息
    const REFUND_METHOD_NATURE_MONTH = 6;//按自然月付息，到期本息
    const REFUND_METHOD_NATURE_QUARTER = 7;//按自然季度付息，到期本息
    const REFUND_METHOD_NATURE_HALF_YEAR = 8;//按自然半年付息，到期本息
    const REFUND_METHOD_NATURE_YEAR = 9;//按自然年付息，到期本息

    /*定义错误级别*/
    const ERROR_SUCCESS = 100;
    const ERROR_NO_EXIST = 101;//不存在标的
    const ERROR_STATUS_DENY = 102;//状态不可投
    const ERROR_NO_BEGIN = 103;//没有开始 根据时间
    const ERROR_OVER = 104;//已经结束 根据时间
    const ERROR_MONEY_FORMAT = 105;//金额格式错误 根据时间
    const ERROR_LESS_START_MONEY = 106;//投资金额小于起投金额
    const ERROR_MONEY_LESS = 107;//余额不足
    const ERROR_MONEY_MUCH = 108;//投资金额大于可投余额
    const ERROR_DIZENG = 109;//不满足递增要求
    const ERROR_MONEY_BALANCE = 110;
    const ERROR_PRO_STATUS = 111;
    const ERROR_CONTRACT = 112;
    const ERROR_TARGET = 113;
    const ERROR_SYSTEM = 199;//系统错误

    const SORT_PRE = 10;
    const SORT_NOW = 20;
    const SORT_FULL = 30;
    const SORT_FOUND = 31;
    const SORT_LIU = 40;
    const SORT_HKZ = 50;
    const SORT_YHK = 60;

    public function scenarios()
    {
        return [
            'del' => ['del_status'],
            'status' => ['status', 'sort', 'full_time'],
            'jixi' => ['jixi_time'],
            'create' => ['title', 'sn', 'cid', 'money', 'borrow_uid', 'expires', 'expires_show', 'yield_rate', 'start_money', 'borrow_uid', 'fee', 'status',
                'description', 'refund_method', 'account_name', 'account', 'bank', 'dizeng_money', 'start_date', 'end_date', 'full_time',
                'is_xs', 'yuqi_faxi', 'order_limit', 'creator_id', 'del_status', 'status', 'isPrivate', 'allowedUids', 'finish_date', 'channel', 'jixi_time', 'sort',
                'jiaxi', 'kuanxianqi', 'isFlexRate', 'rateSteps', 'issuer', 'issuerSn', 'paymentDay', 'isTest', 'filingAmount'],
        ];
    }

    public static function createSN($pre = 'DK')
    {
        $last = self::find()->where(['<=', 'created_at', strtotime(date('Y-m-d').'23:59:59')])->select('sn')->orderBy('sn desc')->one();
        $date = date('Ymd');
        $sn = $pre;
        if (strpos($last['sn'], $date) === false) {
            //没有编号的
            $sn .= $date.'000';
        } else {
            $step = Yii::$app->functions->autoInc(substr($last['sn'], 10, 3));
            $sn .= $date.$step;
        }

        return $sn;
    }

    public static function getProductStatusAll($key = null)
    {
        $data = array(
            self::STATUS_PRE => '预告期',
            self::STATUS_NOW => '募集期',
            self::STATUS_FULL => '满标',
            self::STATUS_FOUND => '成立',
            self::STATUS_LIU => '流标',
            self::STATUS_HUAN => '还款中',
            self::STATUS_OVER => '已还清',
            self::STATUS_FOUND => '募集提前结束',
        );
        if (!empty($key)) {
            return $data[$key];
        }

        return $data;
    }

    public static function getRefundMethod($key = null)
    {
        $data = array(
            self::REFUND_METHOD_DAOQIBENXI => '到期本息',
        );
        if (!empty($key)) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_product';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'borrow_uid', 'yield_rate', 'money', 'start_money', 'dizeng_money', 'start_date', 'end_date', 'expires', 'cid', 'description', 'refund_method'], 'required'],
            ['finish_date', 'required', 'when' => function ($model) {
                return $model->is_fdate == 1 && !empty($model->finish_date);
            },  'whenClient' => "function (attribute, value) {
                return $('#onlineproduct-is_fdate').parent().hasClass('checked');
            }"],
            [['cid', 'is_xs', 'borrow_uid', 'refund_method', 'expires', 'full_time', 'del_status', 'status', 'order_limit', 'creator_id'], 'integer'],
            [['yield_rate', 'fee', 'money', 'start_money', 'dizeng_money', 'yuqi_faxi', 'jiaxi'], 'number'],
            [['isPrivate', 'kuanxianqi'], 'integer'],
            ['isPrivate', 'default', 'value' => 0],
            [['is_xs', 'kuanxianqi', 'is_fdate'], 'default', 'value' => 0],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 32],
            [['allowedUids'], 'string', 'max' => 128],
            [['allowedUids'], 'match', 'pattern' => '/^1[34578]\d{9}((,)1[34578]\d{9})*$/', 'message' => '{attribute}必须是以英文逗号分隔的手机号,首尾不得加逗号'],
            [['sn'], 'string', 'max' => 32],
            ['sn', 'unique', 'message' => '编号已占用'],
            [['expires_show'], 'string', 'max' => 50],
            [['del_status', 'funded_money'], 'default', 'value' => 0],
            [['money', 'start_money', 'dizeng_money', 'yuqi_faxi', 'fee', 'filingAmount'], 'double'],
            [['yuqi_faxi', 'fee'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['money'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['start_money'], 'compare', 'compareAttribute' => 'money', 'operator' => '<', 'type' => "number"],//以数字做比较
            [['yield_rate', 'jiaxi'], 'compare', 'compareValue' => 100, 'operator' => '<='],
            [['yield_rate', 'jiaxi', 'kuanxianqi'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['jiaxi'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9])?$/', 'message' => '加息利率只允许有一位小数'],
            [['jiaxi'], 'compare', 'compareValue' => 10, 'operator' => '<='],
            [['jiaxi'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['money'], 'compare', 'compareValue' => 1000000000, 'operator' => '<='],
            [['money'], 'compare', 'compareValue' => 1, 'operator' => '>='],
            ['status', 'checkDealStatus'],
            ['expires', 'checkExpires'],
            ['epayLoanAccountId', 'default', 'value' => ''],
            [['start_date', 'end_date', 'finish_date'], 'checkDate'],
            ['isFlexRate', 'integer'],
            ['rateSteps', 'string', 'max' => 500],
            ['rateSteps', 'required', 'when' => function ($model) {
                return $model->isFlexRate == 1;
            },  'whenClient' => "function (attribute, value) {
                return $('#onlineproduct-isflexrate').parent().hasClass('checked');
            }"],
            [['rateSteps'], 'checkRateSteps'],
            ['paymentDay', 'integer'],
            ['paymentDay', 'default', 'value' => 20],    //固定还款日,默认值每月20号,范围为1到28
            ['paymentDay', 'compare', 'compareValue' => 1, 'operator' => '>='],
            ['paymentDay', 'compare', 'compareValue' => 28, 'operator' => '<='],
            ['isTest', 'integer'],
            [['start_money', 'dizeng_money'], 'checkMoney'],
        ];
    }

    //起投金额、递增金额判断
    public function checkMoney()
    {
        if ($this->isTest) {
            if ($this->start_money < 0) {
                $this->addError('start_money', '起投金额的值不能小于零');
            }
            if ($this->dizeng_money < 0) {
                $this->addError('dizeng_money', '递增金额的值不能小于零');
            }
        } else {
            if ($this->start_money <= 0) {
                $this->addError('start_money', '起投金额的值必须大于零');
            }
            if ($this->dizeng_money < 1) {
                $this->addError('dizeng_money', '递增金额的值必须大于或等于1');
            }
        }
    }

    public function checkRateSteps()
    {
        $rateSteps = $this->rateSteps;
        $isFlexRate = $this->isFlexRate;
        $yield_rate = $this->yield_rate;//项目利率
        if (boolval($isFlexRate)) {
            if (empty($rateSteps)) {
                $this->addError('rateSteps', '浮动利率不能为空');
            }
            if (!RateSteps::checkRateSteps($rateSteps, floatval($yield_rate))) {
                $this->addError('rateSteps', '浮动利率格式错误');
            }
        }
    }

    public function checkDate()
    {
        $start = is_integer($this->start_date) ? $this->start_date : strtotime($this->start_date);
        $end = is_integer($this->end_date) ? $this->end_date : strtotime($this->end_date);
        if ($start > $end) {
            $this->addError('start_date', '募集开始时间小于募集结束时间小于项目结束日');
            $this->addError('end_date', '募集开始时间小于募集结束时间小于项目结束日');
        }
        if (null !== $this->finish_date && '' !== $this->finish_date && 0 != $this->finish_date) {
            $finish = is_integer($this->finish_date) ? $this->finish_date : strtotime($this->finish_date);
            if ($start > $finish) {
                $this->addError('start_date', '募集开始时间小于募集结束时间小于项目结束日');
                $this->addError('finish_date', '募集开始时间小于募集结束时间小于项目结束日');
            }
            if ($end > $finish) {
                $this->addError('end_date', '募集开始时间小于募集结束时间小于项目结束日');
                $this->addError('finish_date', '募集开始时间小于募集结束时间小于项目结束日');
            }
        }

        return true;
    }

    /**
     * 获取项目天数.
     */
    public function getSpanDays()
    {
        return 0 === $this->finish_date ? $this->expires : \Yii::$app->functions->timediff(strtotime(date('Y-m-d', $this->start_date)), strtotime(date('Y-m-d', $this->finish_date)))['day'];
    }

    /**
     * 验证如果是满标、还款中、已还款不能编辑.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkDealStatus($attribute, $params)
    {
        $status = $this->$attribute;
        if (in_array($status, [self::STATUS_FULL, self::STATUS_HUAN, self::STATUS_OVER])) {
            $this->addError($attribute, '此时项目状态不允许编辑了');
        } else {
            return true;
        }
    }

    /**
     * 验证项目天数 <= 产品到期日 - 募集开始时间.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkExpires($attribute, $params)
    {
        $expires = $this->$attribute;
        $diff = \Yii::$app->functions->timediff(strtotime($this->start_date),  strtotime($this->finish_date));
        if ($expires > $diff['day']) {
        } else {
            return true;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '名称',
            'sn' => '项目编号',
            'cid' => '分类',
            'borrow_uid' => '融资用户ID',
            'yield_rate' => '年利率',
            'jiaxi' => '加息利率',
            'fee' => '手续费',
            'expires_show' => '项目期限文字显示',
            'refund_method' => '还款方式',
            'expires' => '借款期限',
            'kuanxianqi' => '宽限期',
            'money' => '融资总额',
            'start_money' => '起投金额',
            'dizeng_money' => '递增金额',
            'start_date' => '融资开始日期',
            'end_date' => '融资结束日期',
            'description' => '项目介绍',
            'full_time' => '满标时间',
            'jixi_time' => '计息开始时间',
            'del_status' => '删除状态',
            'account_name' => '账户名称',
            'finish_date' => '产品到期日',
            'bank' => '银行',
            'contract_type' => '使用固定模板',
            'status' => '标的进展',
            'yuqi_faxi' => '逾期罚息',
            'order_limit' => '限制投标人次',
            'isPrivate' => '是否定向标',
            'is_xs' => '是否新手标',
            'is_fdate' => '是否使用截止日期',
            'allowedUids' => '定向标用户',
            'creator_id' => '创建者',
            'updated_at' => '创建时间',
            'created_at' => '更新时间',
            'isFlexRate'=>'是否启用浮动利率',
            'rateSteps'=>'浮动利率',
            'paymentDay' => '固定还款日',
            'isTest' => '是测试标',
        ];
    }

    /**
     * 状态检测.
     *
     * @param type $id
     *
     * @return bool
     */
    public static function checkStatus($id = null, $pro = null)
    {
        if (empty($id) || empty($pro)) {
            return true;
        }
        $newAttr = $pro->getAttributes();
        $oldAttr = $pro->getOldAttributes();
        $new_status = intval($newAttr['status']);
        $bool = true;//默认允许修改
        //先判断状态
        switch ($new_status) {
            case self::STATUS_PRE:
                $bool = self::checkPre();
                break;
            case self::STATUS_NOW:
                $bool = self::checkNow();
                break;
            case self::STATUS_FULL:
                break;
            case self::STATUS_LIU:
                $bool = self::cancelOrder($id, $oldAttr);
                break;
            case self::STATUS_HUAN;
                break;
            case self::STATUS_OVER:
                break;
            case self::STATUS_FOUND:
                break;
        }

        return $bool;
    }

    /*预告期判断*/
    public static function checkPre($oldAttr)
    {
        //如果状态属于预告期，还款中或者已经结束的不可以撤标
        if (in_array($oldAttr['status'], [self::STATUS_LIU, self::STATUS_FULL, self::STATUS_NOW, self::STATUS_FOUND, self::STATUS_HUAN, self::STATUS_OVER])) {
            return false;
        }

        return true;
    }

    /*流标判断*/
    public static function cancelOrder($id, $oldAttr)
    {
        //如果状态属于预告期，还款中或者已经结束的不可以撤标
        if (in_array($oldAttr['status'], array(self::STATUS_PRE, self::STATUS_HUAN, self::STATUS_OVER))) {
            return false;
        }
        $order = new \common\models\order\OnlineOrder();
        $res = $order->cancelOnlinePro($id);

        return $res == 1 ? true : false;
    }

    /*
     * 获取标的合同
     */
    public function getContract()
    {
        $contract_type = $this->contract_type;
        $cond['type'] = $contract_type;
        if ($contract_type == 1) {
            $cond['pid'] = $this->id;
        }

        return \common\models\contract\ContractTemplate::find()->where($cond)->asArray()->one();
    }

    public function getLoanId()
    {
        return $this->id;
    }

    public function getLoanName()
    {
        return $this->title;
    }

    public function getLoanAmount()
    {
        return $this->money;
    }

    public function getLegalAmount()
    {
        return $this->funded_money;
    }

    public function getLoanExpireDate()
    {
        return $this->end_date;
    }

    /**
     * 创建是否成功?
     *
     * @param OnlineProduct $loan
     * @param Borrower      $borrower
     *
     * @return bool
     */
    public static function createLoan(OnlineProduct $deal, Borrower $borrower)
    {
        //如果已经在联动一侧生成。则无法在创立。错误代码将会显示00240213【当前标的状态不可投资，请更新标的状态为开标】,如果商户不存在00060711提示商户[2]未开通
        $resp = \Yii::$container->get('ump')->registerLoan($deal, $borrower);
        if ($resp->isSuccessful() && '92' === $resp->get('project_state')) {
            return $resp->get('project_account_id');
        } else {
            $umpLoan = self::getUmpLoan($deal->id);
            if (null === $umpLoan) {
                throw new \Exception($resp->get('ret_msg'));
            }

            return $umpLoan->get('project_account_id');
        }
    }

    /**
     * 获取联动一侧标的详情.
     *
     * @param type $id
     *
     * @return type
     *
     * @throws Exception
     */
    public static function getUmpLoan($id)
    {
        $resp = \Yii::$container->get('ump')->getLoanInfo($id);
        if (!$resp->isSuccessful()) {
            return;
        }

        return $resp;
    }

    /**
     * 用于加息时候，利率的显示.
     *
     * @return decimal
     */
    public function getBaseRate()
    {
        return static::calcBaseRate($this->yield_rate, $this->jiaxi);
    }

    /**
     * 用于加息时候，利率的显示.
     */
    public static function calcBaseRate($yr, $jiaxi)
    {
        return null === $jiaxi ? bcmul($yr, 100, 2) : bcsub(bcmul($yr, 100, 2), $jiaxi, 2);
    }

    public function getLoanExpires()
    {
        if (self::REFUND_METHOD_DAOQIBENXI === (int) $this->refund_method) {
            //到期本息
            return ['v' => $this->expires, 'unit' => '天'];
        } elseif (
                self::REFUND_METHOD_MONTH === (int) $this->refund_method
                || self::REFUND_METHOD_QUARTER === (int) $this->refund_method
                || self::REFUND_METHOD_HALF_YEAR === (int) $this->refund_method
                || self::REFUND_METHOD_YEAR === (int) $this->refund_method
                ) {
            return ['v' => $this->expires, 'unit' => '个月'];
        } else {
            throw new Exception('还款方式错误');
        }
    }

    public function getMobiles()
    {
        if ('' !== $this->allowedUids && null !== $this->allowedUids) {
            $users = \common\models\user\User::find()->where('id in ('.$this->allowedUids.')')->all();
            $mobiles = '';
            foreach ($users as $user) {
                $mobiles .= $user->mobile.',';
            }

            return substr($mobiles, 0, strlen($mobiles) - 1);
        } else {
            return '';
        }
    }

    /**
     * 计算项目余额.
     */
    public function getLoanBalance()
    {
        if (intval($this->status) === OnlineProduct::STATUS_FOUND) {
            $dealBalance = 0;
        } else if ($this->status >= OnlineProduct::STATUS_NOW) {
            //募集期的取剩余
            $dealBalance = bcsub($this->money, $this->funded_money);
        } else {
            $dealBalance = $this->money;
        }

        return $dealBalance;
    }

    /**
     * 查询推荐标的区标的列表.
     * 1. 排序按照先推荐标的,后普通标的的顺序进行排序;
     * 2. 根据count的值取查询列表的前count条记录;
     */
    public static function getRecommendLoans($count)
    {
        $loans = self::find()
            ->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE])
            ->limit($count)
            ->orderBy('recommendTime desc, sort asc, id desc')
            ->all();

        return $loans;
    }

    /**
     * 获取项目期限 getDealExpires
     * 上线未成立的项目，项目期限＝产品到期日－当前日；成立后的项目，项目期限＝产品到期日－计息日期+1
     * @return array ['expires' => $expires, 'unit' => $unit]
     * @throws NotFoundHttpException
     */
    public function getDuration()
    {
        //如果 项目 是到期本息 并且 有产品到期日，那么项目期限需要按照指定逻辑进行计算
        if (intval($this->refund_method) === OnlineProduct::REFUND_METHOD_DAOQIBENXI) {
            if ( $this->finish_date > 0) {
                if ($this->jixi_time && $this->is_jixi) {
                    //项目期限＝ 产品到期日－计息日期 + 1；后台确认计息时候已经算好并保存到数据库了
                    $expires = $this->expires;
                } else {
                    //产品到期日－当前日
                    $datetime1 = new \DateTime(date('Y-m-d', $this->finish_date));
                    $datetime2 = new \DateTime(date('Y-m-d', time()));
                    $interval = $datetime1->diff($datetime2);
                    $expires = $interval->days - 1;
                }
            } else {
                $expires = $this->expires;
            }
            $unit = '天';
        } else {
            $expires = $this->expires;
            $unit = '个月';
        }

        return ['value' => $expires, 'unit' => $unit];
    }

    /**
     * 获取融资用户信息.
     */
    public function getBorrower()
    {
        return $this->hasOne(User::className(), ['id' => 'borrow_uid']);
    }

    /**
     * 获取指定时间段的新增资产
     * 用户新增投资资产：两次在途资产（标的状态为 募集中、满表、提前结束、收益中 的标的的累计成功投资金额）相减
     * 如果没有开始时间，表示从项目上线开始
     * 如果没有结束时间，表示一直到程序运行时间
     * @param integer $user_id
     * @param string $start
     * @param string $end
     * @return float
     */
    public static function getInvestmentIncreaseBetween($user_id, $start, $end) {
        $startAt = strtotime($start);
        $endAt = strtotime($end);
        //计算开始时间点的在途资金
        $query = OnlineOrder::find()
            ->select('online_order.order_money')
            ->innerJoin('online_product' ,'online_order.online_pid = online_product.id')
            ->where(['online_product.status' => [2,3,5,7]])
            ->andWhere(['online_order.status' => 1])
            ->andWhere(['online_order.uid' => $user_id]);
        $new_query = clone $query;
        $startTotalAsset = $query->andWhere(['<=', 'online_order.order_time', $startAt])->sum('online_order.order_money');
        //计算开始时间到结束时间的回款
        $money = MoneyRecord::find()
            ->where(['uid' => $user_id])
            ->andWhere(['type' => 4])
            ->andWhere(['between', 'created_at', $startAt, $endAt])
            ->sum('in_money');
        //计算结束时间点的在途资金
        $endTotalAsset = $new_query->andWhere(['<=', 'online_order.order_time', $endAt])->sum('online_order.order_money');
        return floatval($endTotalAsset) - floatval($startTotalAsset) - floatval($money);
    }

    /**
     * 获取项目的募集进度
     * @return int
     */
    public function getProgressForDisplay(){
        if (in_array($this->status, [3, 5, 6, 7])){
            return 100;
        } else {
            return intval($this->finish_rate*100);
        }
    }

    public function getFangkuan(){
        return $this->hasOne(OnlineFangkuan::class,['online_product_id'=> 'id']);
    }

    /**
     * 获取指定标的的所有还款日
     * 注意点： 默认标的的起息日期和截止日期是正确的；如果还款日超过当月最后一天，实际还款日取最后一天
     * demo：调用方式 $load->getPaymentDates()
     * return array 返回所有还款日自然排序后组成的数组,返回 date('Y-m-d',$time) 组成的数组
     * @throws Exception
     */
    public function getPaymentDates()
    {
        $productProcessor = new ProductProcessor();
        //必要信息判断
        if (!$this->jixi_time) {
            throw new Exception();
        }
        //初始化数据项
        $jixi_time = $this->jixi_time;//计息日志
        $finish_time = $this->finish_date;//截止日期
        $method = intval($this->refund_method);//还款方式
        $expires = intval($this->expires);
        $paymentDay = $this->paymentDay;//设定的还款日期
        //计算实际最后一次还款日期
        if ($method === 1) {
            if (!$finish_time) {
                $finish_time = strtotime("+ " . $expires . " day", $jixi_time);
            }
        } else {
            $finish_time = $productProcessor->calcRetDate($expires, $jixi_time);
        }
        //枚举所有还款方式
        //$num 表示期数 $total 表示 每期多少月
        if ($method === 2 || $method === 6) {
            $num = $expires;
            $total = 1;
        } elseif ($method === 3 || $method === 7) {
            $num = ceil($expires / 3);
            $total = 3;
        } elseif ($method === 4 || $method === 8) {
            $num = ceil($expires / 6);
            $total = 6;
        } elseif ($method === 5 || $method === 9) {
            $num = ceil($expires / 12);
            $total = 12;
        }
        //还款日期
        $paymentDays = [];
        if ($method === 1) {
            $paymentDays[] = date('Y-m-d', $finish_time);
        } elseif (in_array($method, [2, 3, 4, 5])) {
            //最后一次还款日期为计算出的 项目截止日期
            for ($i = 1; $i < $num; $i++) {
                //获取当期还款日期
                $time = $productProcessor->calcRetDate($i * $total, $jixi_time);
                $paymentDays[] = date('Y-m-d', $time);
            }
            $paymentDays[] = date('Y-m-d', $finish_time);//最后一个还款日为截止日
        } elseif (in_array($method, [6, 7, 8, 9])) {
            for ($i = 1; $i <= $num; $i++) {
                //获取当期还款时间
                $time = $productProcessor->calcRetDate(($i - 1) * $total, $jixi_time);
                $paymentDay = min(intval($paymentDay), intval(date('t', $time)));//取还款日和当月最后一天的最小值
                $paymentDay = str_pad($paymentDay, 2, '0', STR_PAD_LEFT);
                $m = intval(date('m', $time));
                if ($method === 7) {
                    if ($m <= 3) {
                        $m = '03';
                    } elseif ($m <= 6) {
                        $m = '06';
                    } elseif ($m <= 9) {
                        $m = '09';
                    } else {
                        $m = '12';
                    }
                } elseif ($method === 8) {
                    if ($m <= 6) {
                        $m = '06';
                    } else {
                        $m = '12';
                    }
                } else if ($method === 9) {
                    $m = '12';
                } else {
                    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
                }
                $paymentDate = date('Y', $time) . '-' . $m . '-' . $paymentDay;
                //如果还款时间大于最后一个还款日退出
                if (strtotime($paymentDate) > $finish_time) {
                    break;
                }

                if (strtotime($paymentDate) > $jixi_time) {
                    $paymentDays[] = $paymentDate;
                } else {
                    //如果还款时间小于起息日期，期数+1
                    $num++;
                }
            }
            if (!in_array(date('Y-m-d', $finish_time), $paymentDays)) {
                $paymentDays[] = date('Y-m-d', $finish_time);//最后一个还款日为截止日
            }
        }
        return $paymentDays;
    }


    /**
     * 判断标的是否为按自然时间付息方式
     * @return boolean
     */
    public function isNatureRefundMethod()
    {
        return in_array($this->refund_method, [
            self::REFUND_METHOD_NATURE_MONTH,
            self::REFUND_METHOD_NATURE_QUARTER,
            self::REFUND_METHOD_NATURE_HALF_YEAR,
            self::REFUND_METHOD_NATURE_YEAR,
        ]);
    }
}
