<?php

namespace common\models\order;

use common\models\coupon\UserCoupon;
use common\models\epay\EpayUser;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\user\User;
use EBaoQuan\Client;
use P2pl\OrderTxInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "online_order".
 *
 * @property int    id
 * @property string sn
 * @property string online_pid
 * @property string order_money
 * @property int    order_time
 * @property int    uid
 * @property int    status
 * @property string created_at
 * @property string updated_at
 * @property int    investFrom      投资来源 0表示未知，1表示wap，2表示wx，3表示app，4表示pc
 * @property string couponAmount    代金券使用金额
 * @property float  $yield_rate     实际利率
 */
class OnlineOrder extends ActiveRecord implements OrderTxInterface
{
    //0--投标失败---1-投标成功 2.撤标 3，无效
    const STATUS_FALSE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_CANCEL = 2;

    //投资来源
    const INVEST_FROM_WAP = 1;//wap
    const INVEST_FROM_WX = 2;//微信
    const INVEST_FROM_APP = 3;//app
    const INVEST_FROM_PC = 4;//pc
    const INVEST_FROM_OTHER = 0;//未知

    public $agree = '';
    public $order_return;
    public $drawpwd;
    private $_user = false;

    private $baoquanＤownloadLink;//保全合同下载链接

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_order';
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

    public static function createSN()
    {
        $pre_val = '00';//由于合同里未知的原因导致的错位，换00替换
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }

    public function rules()
    {
        return [
            //[['agree','order_money','drawpwd'], 'required'],
            [['order_money'], 'required'],
            ['drawpwd', 'trim'],
            ['drawpwd', 'validatePassword'],
            [['online_pid', 'order_time', 'uid', 'status', 'investFrom'], 'integer'],
            [['order_money', 'couponAmount', 'paymentAmount'], 'number'],
            [['sn'], 'string', 'max' => 30],
            [['campaign_source'], 'string', 'max' => 50],
            [['status'], 'default', 'value' => 0],
            //['agree', 'required', 'requiredValue'=>true,'message'=>'请确认是否同意隐私权协议条款'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateTradePwd($this->drawpwd, $user->trade_pwd)) {
                $this->addError($attribute, '密码错误.');
            }
        }
    }

    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne($this->uid);
        }

        return $this->_user;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'online_pid' => 'Online Pid',
            'order_money' => '投资金额',
            'order_time' => 'Order Time',
            'uid' => 'Uid',
            'status' => 'Status',
            'drawpwd' => '交易密码',
            'agree' => '同意以上合同事项',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 计算融资百分比.
     */
    public static function getRongziPercert($pro_id = 0)
    {
        $all_money = static::find()->where(['status' => 1, 'online_pid' => $pro_id])->sum('order_money');
        $product = OnlineProduct::findOne($pro_id);

        return bcdiv($all_money, $product->money, 2);
    }

    /**
     * 撤标.
     */
    public function cancelOnlinePro($pid = null)
    {
        $list = static::find()->where(['status' => self::STATUS_SUCCESS, 'online_pid' => $pid])->all();
        $transaction = Yii::$app->db->beginTransaction();
        $bcround = new \common\lib\bchelp\BcRound();
        bcscale(14);
        //$order = new OnlineOrder();
        foreach ($list as $val) {
            /*标的更改状态*/
            $order_model = clone $val;
            $order_model->status = self::STATUS_CANCEL;
            //$order_model->agree=1;
            $ore = self::updateAll($order_model, ' id = '.$order_model->id);
            if (!$ore) {
                $transaction->rollBack();

                return 0;
            }
            $ua = UserAccount::getUserAccount($order_model->uid);

            $ua->freeze_balance = $bcround->bcround(bcsub($ua->freeze_balance, $order_model->order_money), 2);
            if ($ua->freeze_balance * 1 < 0) {
                $transaction->rollBack();

                return 0;
            }
            $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, $order_model->order_money), 2);
            $uare = $ua->save();
            if (!$uare) {
                $transaction->rollBack();

                return 0;
            }
            //资金记录表
            $mrmodel = new MoneyRecord();
            $mrmodel->account_id = $ua->id;
            $mrmodel->sn = MoneyRecord::createSN();
            $mrmodel->type = MoneyRecord::TYPE_CHEBIAO;
            $mrmodel->osn = $order_model->sn;
            $mrmodel->uid = $order_model->uid;
            $mrmodel->out_money = $order_model->order_money;
            $mrmodel->balance = $ua->available_balance;
            $mrmodel->in_money = $order_model->order_money;
            $mrres = $mrmodel->save();
            if (!$mrres) {
                $transaction->rollBack();

                return 0;
            }
            //修改产品为流标
            $online = OnlineProduct::findOne($order_model->online_pid);
            $online->scenario = 'status';
            $online->status = OnlineProduct::STATUS_LIU;
            $opre = $online->save();
            if (!$opre) {
                $transaction->rollBack();

                return 0;
            }
        }
        $transaction->commit();

        return 1;
    }

    /**
     * 获取订单列表
     * $cond 条件.
     */
    public static function getOrderListByCond($cond = array(), $field = '*')
    {
        $data = static::find()->where($cond)->select($field)->asArray()->all();

        return $data;
    }

    /**
     * 获取用户托管方平台信息.
     */
    public function getEpayuser()
    {
        return $this->hasOne(EpayUser::className(), ['appUserId' => 'uid']);
    }

    public function getLoanId()
    {
        return $this->online_pid;
    }

    public function getTxSn()
    {
        return $this->sn;
    }

    public function getTxDate()
    {
        return $this->created_at;
    }

    public function getEpayUserId()
    {
        return $this->epayuser->epayUserId;
    }

    public function getAmount()
    {
        return $this->order_money;
    }

    /**
     * 获取投资时间
     */
    public function getOrderDate()
    {
        return date('Y-m-d H:i:s', $this->created_at);
    }

    /**
     * 根据订单号或者对象返回订单.
     *
     * @param 订单号或者订单对象
     * @throws \Exception
     */
    public static function ensureOrder($ordOrSn)
    {
        $ord = null;
        if ($ordOrSn instanceof OnlineOrder) {
            $ord = $ordOrSn;
        } elseif (is_string($ordOrSn)) {
            $ord = OnlineOrder::findOne(['sn' => $ordOrSn]);
            if (null === $ord) {
                throw new \Exception('无此订单记录');
            }
        } else {
            throw new \Exception('参数错误');
        }
        return $ord;
    }

    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    public function getLoan()
    {
        return $this->hasOne(OnlineProduct::className(), ['id' => 'online_pid']);
    }

    /**
     * 获取当前订单的还款计划.
     */
    public function getRepaymentPlan()
    {
        return $this->hasMany(OnlineRepaymentPlan::class, ['order_id' => 'id']);
    }

    /**
     * 获取当前订单预期（实际）收益.
     */
    public function getProceeds()
    {
        $plans = $this->repaymentPlan;
        $amount = 0;
        if ($plans) {
            foreach ($plans as $plan) {
                if (in_array($plan->status, [OnlineRepaymentPlan::STATUS_WEIHUAN, OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM])) {
                    $amount = $amount + $plan->lixi;
                }
            }
        }
        return $amount;
    }

    /**
     * 获取当前订单的最后一次还款时间.
     */
    public function getLastPaymentDate()
    {
        $plans = $this->repaymentPlan;
        if ($plans) {
            $plan = end($plans);
            $date = date('Y-m-d', $plan->refund_time);
        } else {
            $date = date('Y-m-d', $this->loan->finish_date);
        }
        return $date;
    }

    /**
     * 根据订单计算年化投资金额.
     */
    public function getAnnualInvestment()
    {
        if ($this->loan->isAmortized()) {
            $unit = 'm';    //按月计算
        } else {
            $unit = 'd';    //按天计算
        }

        if ('m' === $unit) {
            $base = 12;
        } else {
            $base = 365;
        }

        return bcdiv(bcmul($this->order_money, $this->loan->expires, 14), $base, 2);
    }

    /**
     * 根据订单sn判断该订单是否为所属用户的首次投资订单
     */
    public function isFirstInvestment()
    {
        if ($this->getIsNewRecord()) {
            throw new \Exception('the record should be inserted when calling!');
        }
        $order = OnlineOrder::find()->where(['uid' => $this->uid, 'status' => 1])->orderBy(['order_time' => SORT_ASC, 'id' => SORT_ASC])->one();
        if (null === $order) {
            return false;
        }
        return $this->sn === $order->sn;
    }

    //获取保全合同下载链接
    public function getBaoquanDownloadLink()
    {
        if (is_null($this->baoquanＤownloadLink)) {
            $baoQuan = EbaoQuan::find()->where([
                'itemType' => EbaoQuan::ITEM_TYPE_LOAN_ORDER,
                'type' => EbaoQuan::TYPE_LOAN,
                'success' => 1,
                'uid' => $this->uid,
                'itemId' => $this->id,
            ])->one();

            $this->baoquanＤownloadLink = is_null($baoQuan) ? null : Client::contractFileDownload($baoQuan);
        }
        return $this->baoquanＤownloadLink;
    }

    public function getCoupon()
    {
        return $this->hasMany(UserCoupon::className(), ['order_id' => 'id']);
    }
}
