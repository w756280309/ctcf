<?php

namespace common\models\order;

use yii\behaviors\TimestampBehavior;
use Yii;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\user\User;
use common\models\epay\EpayUser;

/**
 * This is the model class for table "online_order".
 *
 * @property int $id
 * @property string $sn
 * @property string $online_pid
 * @property string $order_money
 * @property int $order_time
 * @property int $uid
 * @property int $status
 * @property string $created_at
 * @property string $updated_at
 */
class OnlineOrder extends \yii\db\ActiveRecord implements \P2pl\OrderTxInterface
{
    //0--投标失败---1-投标成功 2.撤标 3，无效
    const STATUS_FALSE = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_CANCEL = 2;
    const STATUS_WUXIAO = 3;

    public $agree = '';
    public $order_return;
    public $drawpwd;
    private $_user = false;

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

    public static function createSN($pre = 'o_online')
    {
        //$pre_val = Yii::$app->params['bill_prefix'][$pre];
        $pre_val = '00';//由于合同里未知的原因导致的错位，换00替换
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            //[['agree','order_money','drawpwd'], 'required'],
            [['order_money'], 'required'],
            ['drawpwd', 'trim'],
            ['drawpwd', 'validatePassword'],
            [['online_pid', 'order_time', 'uid', 'status', 'created_at', 'updated_at'], 'integer'],
            [['order_money'], 'number'],
            [['sn'], 'string', 'max' => 30],
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

    /**
     * Finds user by [[username]].
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne($this->uid);
        }

        return $this->_user;
    }

    /**
     * {@inheritdoc}
     */
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
     * 计算项目余额.
     *
     * @param type $pro_id
     *
     * @return type
     */
    public static function getOrderBalance($pro_id = 0)
    {
        bcscale(14);
        $all_money = static::find()->where(['status' => 1, 'online_pid' => $pro_id])->sum('order_money');
        if (empty($all_money)) {
            $all_money = 0;
        }
        $product = OnlineProduct::findOne($pro_id);

        return bcsub($product->money, $all_money);
    }

    /**
     * 计算融资百分比.
     *
     * @param type $pro_id
     *
     * @return type
     */
    public static function getRongziPercert($pro_id = 0)
    {
        $all_money = static::find()->where(['status' => 1, 'online_pid' => $pro_id])->sum('order_money');
        $product = OnlineProduct::findOne($pro_id);

        return bcdiv($all_money, $product->money, 2);
    }

    /*撤标*/
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
     * 获取新手标总数
     */
    public static function xsCount($uid) {
        return (int)(new \yii\db\Query())
                ->select('count(id)')
                ->from(self::tableName() . ' o')
                ->innerJoin('online_product p', 'o.online_pid=p.id')
                ->where(['o.uid' => $uid, 'p.is_xs' => 1])->count();
    }
    
    /**
     * 获取用户托管方平台信息.
     *
     * @return UserBanks
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
    
}
