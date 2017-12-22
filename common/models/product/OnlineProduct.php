<?php

namespace common\models\product;

use common\lib\product\ProductProcessor;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineOrder;
use common\models\payment\PaymentLog;
use common\models\user\MoneyRecord;
use common\models\user\User;
use P2pl\Borrower;
use P2pl\LoanInterface;
use Wcg\DateTime\DT;
use Wcg\Interest\Builder;
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
 * @property integer $issuer
 * @property int $isTest
 * @property boolean $allUseCoupon 是否允许使用代金券
 * @property boolean $isLicai 是否为理财计划标识
 * @property int     $pointsMultiple    积分倍数
 * @property bool    $allowTransfer     是否允许转让
 * @property bool    $isCustomRepayment     是否是自定义还款
 * @property bool    $isJixiExamined        计息是否通过审核(当标的是自定义还款时候需要计息审核)
 * @property string  $jixi_time     计息日期
 * @property string  $funded_money  实际募集金额
 * @property string  $finish_date   截止日
 * @property bool    $is_jixi
 * @property string  $internalTitle 副标题（仅供内部使用）
 * @property string  $publishTime   产品上线时间
 * @property int     $kuanxianqi 宽限期
 * @property string  $originalBorrower 底层融资方
 * @property string  $pkg_sn  资产包编号
 * @property boolean $isRedeemable 是否允许主动赎回
 * @property string  $redemptionPeriods 赎回申请开放时段（可支持多个）
 * @property string  $redemptionPaymentDates 赎回付款日（可支持多个）
 */
class OnlineProduct extends \yii\db\ActiveRecord implements LoanInterface
{
    public $is_fdate = 0;//是否启用截止日期
    private $subSn;      //产品子类序号

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
    const REFUND_METHOD_DEBX = 10;//按月付本，等额本息

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
    const SORT_FULL = 66;
    const SORT_FOUND = 31;
    const SORT_LIU = 40;
    const SORT_HKZ = 70;
    const SORT_YHK = 60;

    public function scenarios()
    {
        return [
            'del' => ['del_status'],
            'status' => ['status', 'sort', 'full_time'],
            'jixi' => ['jixi_time'],
            'create' => ['title', 'sn', 'cid', 'money', 'borrow_uid', 'expires', 'expires_show', 'yield_rate', 'start_money', 'borrow_uid', 'fee', 'status', 'description', 'refund_method', 'account_name', 'account', 'bank', 'dizeng_money', 'start_date', 'end_date', 'full_time', 'is_xs', 'yuqi_faxi', 'order_limit', 'creator_id', 'del_status', 'status', 'isPrivate', 'allowedUids', 'finish_date', 'channel', 'jixi_time', 'sort', 'jiaxi', 'kuanxianqi', 'isFlexRate', 'rateSteps', 'issuer', 'issuerSn', 'paymentDay', 'isTest', 'filingAmount', 'allowUseCoupon', 'allowRateCoupon',  'tags', 'isLicai', 'pointsMultiple', 'allowTransfer', 'isCustomRepayment', 'internalTitle', 'balance_limit', 'originalBorrower', 'pkg_sn', 'isRedeemable', 'redemptionPeriods', 'redemptionPaymentDates'],
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
            [['title', 'borrow_uid', 'yield_rate', 'money', 'start_money', 'dizeng_money', 'start_date', 'end_date', 'expires', 'cid', 'description', 'refund_method', 'issuer', 'issuerSn'], 'required'],
            ['finish_date', 'required', 'when' => function ($model) {
                return $model->is_fdate == 1 && !empty($model->finish_date);
            },  'whenClient' => "function (attribute, value) {
                return $('#onlineproduct-is_fdate').parent().hasClass('checked');
            }"],
            [['cid', 'is_xs', 'borrow_uid', 'refund_method', 'expires', 'full_time', 'del_status', 'status', 'order_limit', 'creator_id', 'pointsMultiple'], 'integer'],
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
            ['tags', 'string', 'max' => 255],
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
            ['paymentDay', 'compare', 'compareValue' => 1, 'operator' => '>=', 'skipOnEmpty' => true],
            ['paymentDay', 'compare', 'compareValue' => 31, 'operator' => '<=', 'skipOnEmpty' => true],
            ['paymentDay', 'validatePaymentDay'],
            [['isTest', 'allowUseCoupon'], 'integer'],
            [['start_money', 'dizeng_money'], 'checkMoney'],
            ['tags', 'checkTags'],
            [['title', 'internalTitle'], 'trim'],  //去掉项目名称及项目副标题两边多余的空格
            ['allowTransfer', 'boolean'],
            ['internalTitle', 'string', 'max' => 60],
            ['kuanxianqi', 'validateGraceDay'],
            ['balance_limit', 'number'],
            ['balance_limit', 'default', 'value' => 0],
            [['originalBorrower'], 'string', 'max' => 20],
            ['isRedeemable', 'integer'],
            [['redemptionPeriods', 'redemptionPaymentDates'], 'required', 'when' => function ($model) {
                return 1 === $model->isRedeemable;
            }, 'whenClient' => "function (attribute, value) {
                return $('#onlineproduct-isredeemable').parent().hasClass('checked');
            }"],
            ['redemptionPeriods', 'checkRedemptionPeriods'],
        ];
    }

    //验证固定还款日
    public function validatePaymentDay()
    {
        if ($this->isNatureRefundMethod()) {
            switch ($this->refund_method) {
                case OnlineProduct::REFUND_METHOD_YEAR:
                    if ($this->paymentDay > 31) {
                        $this->addError('paymentDay', '按自然年付息时候，固定还款日不能超过 31 号');
                    }
                    break;
                case OnlineProduct::REFUND_METHOD_NATURE_HALF_YEAR:
                case OnlineProduct::REFUND_METHOD_NATURE_QUARTER:
                    if ($this->paymentDay > 30) {
                        $this->addError('paymentDay', '按自然半年或按自然季付息时候，固定还款日不能超过 30 号');
                    }
                    break;
                case OnlineProduct::REFUND_METHOD_NATURE_MONTH:
                    if ($this->paymentDay > 28) {
                        $this->addError('paymentDay', '按自然月付息时候，固定还款日不能超过 28 号');
                    }
                    break;

            }
        }
    }

    /**
     * 宽限期做基本校验
     * 注: 当标的设置了宽限期时候，宽限期 < 截止日 - 当期日期 - 1
     */
    public function validateGraceDay()
    {
        if ($this->kuanxianqi > 0) {
            if ($this->kuanxianqi >= $this->expires) {
                $this->addError('kuanxianqi', "宽限期必须小于项目期限");
            }
            $days = (new \DateTime())->setTimestamp($this->finish_date)->diff(new \DateTime(date('Y-m-d')))->days;
            if ($this->kuanxianqi >= $days - 1) {
                $this->addError('kuanxianqi', "宽限期必须小于 截止日 - 当期日期 - 1");
            }
        }
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

    /**
     * 检验项目的标签
     */
    public function checkTags()
    {
        if ($this->tags) {
            $tags = explode('，', $this->tags);
            if (count($tags) > 2) {
                $this->addError('tags', '当前标签个数不能超过2个');
            }
            foreach ($tags as $num => $tag) {
                $len = 0;
                for ($i = 0; $i < mb_strlen($tag, 'UTF-8'); $i++) {
                    if (is_numeric(mb_substr($tag, $i, 1, 'UTF-8'))) {
                        $len += 0.5;
                    } else {
                        ++$len;
                    }
                }
                if ($len > 4) {
                    $this->addError('tags', '第'.($num + 1).'个标签大于4个字');
                }
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

    public function checkRedemptionPeriods()
    {
        $isRedeemable = $this->isRedeemable;
        $redemptionPeriods = $this->redemptionPeriods;
        if ($isRedeemable) {
            if (empty($redemptionPeriods)) {
                $this->addError('redemptionPeriods', '赎回申请开放时段不能为空');
            }
            if (!RedeemHelper::checkRedemptionPeriods($redemptionPeriods)) {
                $this->addError('redemptionPeriods', '赎回申请开放时段格式错误');
            }
        }
    }

    public function checkRedemptionPaymentDates()
    {
        $isRedeemable = $this->isRedeemable;
        $redemptionPaymentDates = trim($this->redemptionPaymentDates, ',');
        if ($isRedeemable) {
            if ('' === $redemptionPaymentDates) {
                $this->addError('redemptionPaymentDates', '赎回付款日不能为空');
            }
            $redemptionPaymentDates = explode(',', $redemptionPaymentDates);
            foreach ($redemptionPaymentDates as $redemptionPaymentDate) {
                if (false === strtotime($redemptionPaymentDate)) {
                    $this->addError('redemptionPaymentDates', '赎回付款日格式错误');
                    break;
                }
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
            if (date('Y-m-d', $end) >= date('Y-m-d', $finish)) {
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
        if (self::REFUND_METHOD_DAOQIBENXI === $this->refund_method) {
            $days = $this->expires;
        } else {
            $startDate = date('Y-m-d', $this->start_date);

            $days = Yii::$app->functions->timediff(strtotime($startDate), strtotime($startDate.' + '.$this->expires.' months'))['day'];
        }

        return $days;
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
     * 验证项目天数 < 产品到期日 - 募集开始时间.
     *
     * @param type $attribute
     * @param type $params
     *
     * @return bool
     */
    public function checkExpires($attribute, $params)
    {
        $expires = $this->$attribute;
        if ($expires <= 0) {
            $this->addError($attribute, '项目期限必须大于 0 天');
        }
        if (!empty($this->finish_date) && !empty($this->start_date)) {
            $diff = (new \DateTime(date('Y-m-d', $this->start_date)))->diff(new \DateTime(date('Y-m-d', $this->finish_date)))->days;
            if ($expires >= $diff) {
                $this->addError($attribute, '项目期限必须小于 截止日 - 募集开始时间');
            }
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
            'is_xs' => '是否新手专享标',
            'is_fdate' => '是否使用截止日期',
            'allowedUids' => '定向标用户',
            'creator_id' => '创建者',
            'updated_at' => '创建时间',
            'created_at' => '更新时间',
            'isFlexRate'=>'是否启用浮动利率',
            'rateSteps'=>'浮动利率',
            'paymentDay' => '固定还款日',
            'isTest' => '是测试标',
            'issuer' => '发行方',
            'issuerSn' => '发行方项目编号',
            'allowUseCoupon' => '是否可以使用代金券',
            'allowRateCoupon' => '是否可以使用加息券',
            'isLicai' => '是否为理财计划标识',
            'pointsMultiple' => '积分倍数',
            'allowTransfer' => '允许转让',
            'isCustomRepayment' => '是否是自定义还款',
            'internalTitle' => '项目副标题',
            'balance_limit' => '累计资金额',
            'originalBorrower' => '底层融资方',
            'pkg_sn' => '资产包编号',
            'isRedeemable' => '是否允许主动赎回',
            'redemptionPeriods' => '赎回申请开放时段',
            'redemptionPaymentDates' => '赎回付款日',
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
     */
    public static function calcBaseRate($yr, $jiaxi)
    {
        if (null === $jiaxi) {
            $baseRate = bcmul($yr, 100, 2);
        } else {
            $baseRate = bcsub(bcmul($yr, 100, 2), $jiaxi, 2);
        }

        return $baseRate > 0 ? $baseRate : 0;
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
        if ( in_array(intval($this->status), [3 ,5 ,6 ,7]) || $this->end_date < time()) {
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
     * 获得一个处于预告期及募集中的新手标
     */
    public static function getXsLoan()
    {
        return self::find()
            ->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE, 'is_xs' => true])
            ->andWhere(['in', 'status', [self::STATUS_PRE, self::STATUS_NOW]])
            ->orderBy('sort desc, finish_rate desc, id desc')
            ->one();
    }

    /**
     * 查询推荐标的区标的列表.
     * 1. 排序按照先推荐标的,后普通标的的顺序进行排序;
     * 2. 根据count的值取查询列表的前count条记录;
     * 3. $requireNew=true推荐列表里包含预告期及募集中新手标且排在前面
     */
    public static function getRecommendLoans($count, $requireNew = false)
    {
        $count = intval($count);

        $query = self::find();
        if ($requireNew) {
            $query->select('*')
                ->addSelect(['xs_status' => 'if(is_xs = 1 && status < 3, 1, 0)'])
                ->orderBy('xs_status desc, recommendTime desc, sort asc, finish_rate desc, id desc');
        } else {
            $query->orderBy('recommendTime desc, sort asc, finish_rate desc, id desc');
        }

        $query->where(['isPrivate' => 0, 'del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE]);

        //判断当前用户投资额
        $user = Yii::$app->user->getIdentity();
        $balance = 0;
        if (!is_null($user)) {
            $balance = $user->getJGMoney();
        }

        if ($balance < 50000) {
            $query->andWhere(['isLicai' => false]);
            $query->andWhere("NOT((cid = 2) and if(refund_method = 1, expires > 180, expires > 6))");
        }
        $query->limit($count);

        return 1 === $count ? $query->one() : $query->all();
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
        $expires = max($expires, 0);
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
        if (in_array($this->status, [3, 5, 6, 7]) || $this->end_date < time()){
            return 100;
        } else {
            $finishRate = round(bcdiv($this->funded_money, $this->money, 14), 2);
            if ($this->funded_money > 0 && $this->funded_money < $this->money) {
                $finishRate = max($finishRate, 0.01);
                $finishRate = min($finishRate, 0.99);
            }
            return $finishRate * 100;
        }
    }

    public function getFangkuan()
    {
        return $this->hasOne(OnlineFangkuan::class, ['online_product_id' => 'id']);
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
        return RepaymentHelper::calcRepaymentDate($this->getStartDate(),
            $this->getEndDate(),
            $this->refund_method,
            $this->expires,
            $this->paymentDay,
            $this->isCustomRepayment
        );
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

    /**
     * 判断当期标的是否在宽限期
     * @return bool
     */
    public function isInGracePeriod()
    {
        if ($this->finish_date && $this->kuanxianqi > 0 && intval($this->refund_method) === OnlineProduct::REFUND_METHOD_DAOQIBENXI) {
            $start = date('Y-m-d', strtotime('- ' . $this->kuanxianqi . ' day', $this->finish_date));
            $date = date('Y-m-d');
            $end = date('Y-m-d', $this->finish_date);
            return $date >= $start && $date < $end;
        } else {
            return false;
        }
    }

    /**
     * 获取指定标的的剩余期限
     * 到期本息返回天数；分期返回月数和天数（不足一月就不返回月数）
     * @return array
     */
    public function getRemainingDuration()
    {
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('-1 day', $this->finish_date));

        if ($startDate > $endDate) {
            return ['days' => 0];
        }
        $method = intval($this->refund_method);
        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === $method) {
            $day = (new \DateTime($startDate))->diff(new \DateTime($endDate))->days;
            $res = ['days' => $day];
        } else {
            $diff = (new DT($startDate))->humanDiff(new DT($endDate));
            if (!empty($diff) && isset($diff['y']) && isset($diff['m']) && isset($diff['d'])) {
                $y = $diff['y'];
                $m = $diff['m'];
                $d = $diff['d'];
                $m = $y * 12 + $m;
                if ($m > 0) {
                    $res = ['days' => $d, 'months' => $m];
                } else {
                    $res = ['days' => $d];
                }
            } else {
                $res = ['days' => 0];
            }
        }
        return $res;
    }

    /**
     * 获取发行方名称.
     */
    public function getIssuerInfo()
    {
        return $this->hasOne(Issuer::class, ['id'=> 'issuer']);
    }

    /**
     * 获取标的的融资用户名称.
     */
    public function getAffiliatorName()
    {
        if ($this->borrow_uid) {
            $user = User::find()->where(['type' => User::USER_TYPE_ORG, 'id' => $this->borrow_uid])->one();
            if ($user && $user->org_name) {
                return $user->org_name;
            }
        }
        return null;
    }

    /**
     * 初始化对象.
     */
    public static function initNew()
    {
        return new self([
            'sn' => OnlineProduct::createSN(),
            'sort' => OnlineProduct::SORT_PRE,
            'epayLoanAccountId' => '',
            'fee' => 0,
            'funded_money' => 0,
            'full_time' => 0,
            'yuqi_faxi' => 0,
            'allowUseCoupon' => true,
            'allowRateCoupon' => true,
            'paymentDay' => 20,
            'pointsMultiple' => 1,
        ]);
    }

    //判断是否分期
    public function isAmortized()
    {
        return self::REFUND_METHOD_DAOQIBENXI !== intval($this->refund_method);
    }

    /**
     * 返回截止至该还款日应该计息的天数
     * 仅限于已经计息的到期本息项目使用且还款日处于起息日至到期日(不包含该天)之间
     * 调用方式：
     * if (!$deal->isAmortized()
     *     && false !== strtotime($repayDate)
     *     && deal->is_jixi
     *     && $repayDate >= date('Y-m-d', $deal->jixi_time)
     *     && $repayDate < date('Y-m-d', $deal->finish_date)
     * ) {
     *      $days = $deal->getHoldingDays($repayDate);
     * }
     *
     * @param  string $repayDate
     *
     * @return int    $days
     */
    public function getHoldingDays($repayDate)
    {
        $qixiDateTime = new \DateTime(date('Y-m-d', $this->jixi_time));
        if ($this->isInGracePeriod()) {
            $days = (int) (new \DateTime($repayDate))->diff($qixiDateTime)->days;//计算当前时间到计息日期的天数
        } else {
            $graceFirstDay = date('Y-m-d', strtotime('- ' . $this->kuanxianqi . ' day', $this->finish_date));
            $days = (int) (new \DateTime($graceFirstDay))->diff($qixiDateTime)->days;
        }

        return $days;
    }

    /**
     * 判断一个标的是否可以转让.
     *
     * 1. 分期项目期限超过6个月可转;
     * 2. 到期本息项目期限超过180天可转;
     * 3. 标的本身是否允许转让
     */
    public function allowTransfer()
    {
        if (!$this->allowTransfer) {
            return false;
        }
        if (!$this->isAmortized()) {
            if ($this->expires <= Yii::$app->params['credit']['loan_daoqi_limit']) {
                return false;
            }
        } else {
            if ($this->expires <= Yii::$app->params['credit']['loan_fenqi_limit']) {
                return false;
            }
        }

        return true;
    }

    /**
     * 是否允许标的还款.
     */
    public function allowRepayment()
    {
        if (
            $this->fangkuan
            && in_array($this->fangkuan->status, [
                OnlineFangkuan::STATUS_FANGKUAN,
                OnlineFangkuan::STATUS_TIXIAN_APPLY,
                OnlineFangkuan::STATUS_TIXIAN_SUCC,
            ])
        ) {
            return true;
        }

        return false;
    }

    /**
     * 获取一个特定的非定向标的的正式标.
     *
     * 1. 优先获取募集中项目且募集比例高的项目;
     * 2. 如果没有募集中项目,就按照标的ID倒序排列, 取最新的一个;
     */
    public static function fetchSpecial(array $cond = [])
    {
        $loan = self::findSpecial($cond)
            ->andWhere(['status' => OnlineProduct::STATUS_NOW])
            ->andWhere(['<', 'finish_rate', 1])
            ->orderBy(['finish_rate' => SORT_DESC, 'id' => SORT_DESC])
            ->one();

        if (null === $loan) {
            $loan = self::findSpecial($cond)
                ->orderBy(['id' => SORT_DESC])
                ->one();
        }

        return $loan;
    }

    /**
     * 根据指定条件获取一个非定向标的的正式标Query.
     */
    public static function findSpecial(array $cond = [])
    {
         $query = self::find()
             ->where([
                'online_status' => true,
                'del_status' => false,
                'isPrivate' => false,
                'isTest' => false,
            ]);

        if (!empty($cond)) {
            $query->andWhere($cond);
        }

        return $query;
    }

    /**
     * 计算预期收益
     * 注:
     *  1. 等额本息使用旺财谷计算逻辑，利息是使用四舍五入法
     *  2. 其余还款方式的计算结果都是舍去法计算
     *
     * @param $money        string      投资金额
     * @param $refundMethod int         还款方式
     * @param $expires      int         项目期限，到期本息项目期限单位为天，其他类型还款方式项目期限为月
     * @param $rate         string      利率(实际购买利率)
     *
     * @return string
     */
    public static function calcExpectProfit($money, $refundMethod, $expires, $rate)
    {
        if ($refundMethod === OnlineProduct::REFUND_METHOD_DAOQIBENXI) {
            $expectProfit = bcdiv(
                bcmul(
                    bcmul(
                        $money
                        , $rate
                        , 14
                    )
                    , $expires
                    , 14
                )
                , 365
                , 2
            );
        } elseif ($refundMethod === OnlineProduct::REFUND_METHOD_DEBX) {
            $repayPlan = Builder::create(Builder::TYPE_DEBX)
                ->setStartDate(new DT(date('Y-m-d')))
                ->setMonth($expires)
                ->setRate($rate)
                ->build($money);
            $expectProfit = $repayPlan->getInterest();
        } else {
            $expectProfit = bcdiv(
                bcmul(
                    bcmul(
                        $money
                        , $rate
                        , 14
                    )
                    , $expires
                    , 14
                )
                , 12
                , 2
            );
        }

        return $expectProfit;
    }

    //获取标的截止日
    public function getEndDate()
    {
        if (empty($this->finish_date)) {
            $pp = new ProductProcessor();
            if (!$this->isAmortized()) {
                $endDate = $pp->LoanTerms('d1', date('Y-m-d', $this->jixi_time), $this->expires);
            } else {
                $endDate = date("Y-m-d", $pp->calcRetDate($this->expires, $this->jixi_time));//如果由于29,30,31造成的跨月的要回归到上一个月最后一天
            }
        } else {
            $endDate = date('Y-m-d', $this->finish_date);
        }
        return $endDate;
    }

    public function getSuccessOrders()
    {
        return $this->hasMany(OnlineOrder::className(), ['online_pid' => 'id'])
            ->andFilterWhere(['online_order.status' => OnlineOrder::STATUS_SUCCESS]);
    }

    /**
     * 获取产品子类序号.
     */
    public function getSubSn()
    {
        if (is_null($this->subSn)) {
            if ($this->is_xs) {
                $count = (int)OnlineProduct::find()
                    ->where([
                        'is_xs' => true,
                        'del_status' => false,
                        'isPrivate' => false,
                        'isTest' => $this->isTest,
                    ])
                    ->andWhere("date(publishTime) = date(:pubDate) and created_at < :createAt", [
                        'pubDate' => $this->publishTime,
                        'createAt' => $this->created_at,
                    ])
                    ->count();

                return date('md', strtotime($this->publishTime)) . sprintf("%02d", ($count + 1));
            }
        }

        return $this->subSn;
    }

    //获取计息日
    public function getStartDate()
    {
        return date('Y-m-d', $this->jixi_time);
    }

    //获取还款方式
    public function getRefundMethod()
    {
        return intval($this->refund_method);
    }

    //获取代金券贴现状态
    public function isCouponAmountTransferred()
    {
        $paymentLog = PaymentLog::findOne(['loan_id' => $this->id, 'ref_type' => 0]);
        $couponTransfer = false;
        if (!is_null($paymentLog)) {
            $umpResp = Yii::$container->get('ump')->getCouponTransferInfo($paymentLog);
            if ($umpResp->get('ret_code') === '0000') {
                $couponTransfer = true;
            }
        }

        return $couponTransfer;
    }

    //获取加息券贴现状态
    public function isBonusAmountTransferred()
    {
        $paymentLog = PaymentLog::findOne(['loan_id' => $this->id, 'ref_type' => 1]);
        $bonusTransfer = false;
        if (!is_null($paymentLog)) {
            $umpResp = Yii::$container->get('ump')->getCouponTransferInfo($paymentLog);
            if ($umpResp->get('ret_code') === '0000') {
                $bonusTransfer = true;
            }
        }

        return $bonusTransfer;
    }

    //获取加息券贴现金额
    public function getBonusAmount()
    {
        $bonusAmount = '0';
        $paymentLog = PaymentLog::findOne(['loan_id' => $this->id, 'ref_type' => 1]);
        if (null !== $paymentLog) {
            $bonusAmount = $paymentLog->amount;
        }

        return $bonusAmount;
    }
}
