<?php

namespace common\models\order;

use common\models\draw\DrawManager;
use common\models\epay\EpayUser;
use common\models\product\OnlineProduct;
use common\models\tx\DrawRecord;
use common\models\user\User;
use common\models\user\UserAccount;
use P2pl\LoanFkInterface;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii;

/**
 * This is the model class for table "online_fangkuan".
 *
 * @property string $id
 * @property string $sn
 * @property string $order_money
 * @property string $fee
 * @property string $uid
 * @property int $status
 * @property string $admin_id
 * @property string $create_at
 * @property string $updated_at
 */
class OnlineFangkuan extends ActiveRecord implements LoanFkInterface
{
    use \Zii\Model\ErrorExTrait;

    const STATUS_EXAMINED = 1;      //审核通过
    const STATUS_DENY = 2;          //审核不通过
    const STATUS_FANGKUAN = 3;      //放款
    const STATUS_TIXIAN_APPLY = 4;  //提现申请发出
    const STATUS_TIXIAN_SUCC = 5;   //提现成功
    const STATUS_TIXIAN_FAIL = 6;   //提现失败

    public static function createSN($pre = 'fk')
    {
        $pre_val = 'FK';
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_fangkuan';
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
            [['sn', 'admin_id'], 'required'],
            [['order_money', 'fee'], 'number'],
            [['uid', 'status', 'admin_id', 'online_product_id'], 'integer'],
            [['sn'], 'string', 'max' => 30],
            [['remark'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'order_money' => 'Order Money',
            'fee' => 'Fee',
            'uid' => 'Uid',
            'status' => 'Status',
            'remark' => 'remark',
            'admin_id' => 'Admin ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getTxSn()
    {
        return $this->sn;
    }

    public function getTxDate()
    {
        return time();
    }

    public function getLoanId()
    {
        return $this->online_product_id;
    }

    public function getAmount()
    {
        return $this->order_money;
    }

    public function getBorrowerId()
    {
        $loan = OnlineProduct::findOne($this->online_product_id);
        $borrower = User::findOne($loan->borrow_uid);
        return $borrower->epayUser->epayUserId;
    }
}
