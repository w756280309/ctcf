<?php

namespace common\models\product;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "online_product".
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
 * @property string $fazhi
 * @property string $fazhi_up
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
 */
class OnlineProduct extends \yii\db\ActiveRecord
{
    //1预告期、 2募集中,3满标,4流标,5还款中,6已还清7募集提前结束  特对设立阀值得标的进行的设置。
    const STATUS_PRE = 1;
    const STATUS_NOW = 2;
    const STATUS_FULL = 3;
    const STATUS_LIU = 4;
    const STATUS_HUAN = 5;
    const STATUS_OVER = 6;
    const STATUS_FOUND = 7;

    const STATUS_DEL = 1;
    const STATUS_USE = 0;

    const STATUS_ONLINE = 1;
    const STATUS_PREPARE = 0;

    const REFUND_METHOD_DAOQIBENXI = 1;
    const REFUND_METHOD_MONTH = 2;
    const REFUND_METHOD_QUARTER = 3;
    const REFUND_METHOD_YEAR = 4;

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

    public static function getErrorByCode($code = 100)
    {
        $data = [
            self::ERROR_SUCCESS => '',
            self::ERROR_NO_EXIST => '无法找到此标的',
            self::ERROR_STATUS_DENY => '此标的现在不可投',
            self::ERROR_NO_BEGIN => '项目尚未开始或者已经结束',
            self::ERROR_OVER => '项目已经结束',
            self::ERROR_MONEY_FORMAT => '金额格式错误',
            self::ERROR_LESS_START_MONEY => '投资金额小于起投金额',
            self::ERROR_MONEY_LESS => '余额不足',
            self::ERROR_MONEY_MUCH => '投资金额大于可投余额',
            self::ERROR_DIZENG => '不满足递增要求',
            self::ERROR_MONEY_BALANCE => '最后一笔需要投满标的',
            self::ERROR_PRO_STATUS => '更新满标状态错误',
            self::ERROR_CONTRACT => '合同错误',
            self::ERROR_TARGET => '定向标只针对目标用户投标',
            self::ERROR_SYSTEM => '系统异常，请稍后重试',
        ];

        return $data[$code];
    }

    public function scenarios()
    {
        return [
            'del' => ['del_status'],
            'status' => ['status', 'sort', 'full_time'],
            'jixi' => ['jixi_time'],
            'create' => ['title', 'sn', 'cid', 'money', 'borrow_uid', 'expires', 'expires_show', 'yield_rate', 'start_money', 'borrow_uid', 'fee', 'status',
                'description', 'refund_method', 'account_name', 'account', 'bank', 'dizeng_money', 'fazhi', 'fazhi_up', 'start_date', 'end_date', 'full_time', 'is_xs', 'yuqi_faxi', 'order_limit', 'creator_id', 'del_status', 'status', 'target', 'target_uid', 'finish_date', 'channel', 'jixi_time', 'sort', 'jiaxi',],
        ];
    }

    public static function createSN($pre = 'DK')
    {
        $last = self::find()->select('sn')->orderBy('id desc')->one();
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
            [['title', 'borrow_uid', 'yield_rate', 'money', 'start_money', 'dizeng_money', 'start_date', 'end_date', 'expires', 'cid', 'description', 'finish_date'], 'required'],
            [['cid', 'is_xs', 'borrow_uid', 'refund_method', 'expires', 'full_time', 'del_status', 'status', 'order_limit', 'creator_id'], 'integer'],
            [['yield_rate', 'fee', 'money', 'start_money', 'dizeng_money', 'fazhi', 'fazhi_up', 'yuqi_faxi', 'jiaxi',], 'number'],
            [['fazhi', 'fazhi_up', 'target'], 'integer'],
            ['target', 'default', 'value' => 0],
            ['is_xs', 'default', 'value' => 0],
            [['description'], 'string'],
            [['title', 'target_uid'], 'string', 'max' => 128],
            [['target_uid'], 'match', 'pattern' => '/^\d+((,)\d+)*$/', 'message' => '{attribute}格式不正确必须以英文逗号分隔'],
            [['sn'], 'string', 'max' => 32],
            ['sn', 'unique', 'message' => '编号已占用'],
            [['expires_show'], 'string', 'max' => 50],
            [['del_status', 'funded_money'], 'default', 'value' => 0],
            [['fazhi', 'money', 'start_money', 'dizeng_money', 'fazhi_up', 'yuqi_faxi', 'fee'], 'double'],
            [['yuqi_faxi', 'fee'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['money', 'start_money'], 'compare', 'compareValue' => 0, 'operator' => '>'],
            [['dizeng_money'], 'compare', 'compareValue' => 1, 'operator' => '>='],
            [['start_money', 'fazhi', 'fazhi_up'], 'compare', 'compareAttribute' => 'money', 'operator' => '<'],
            [['fazhi_up'], 'compare', 'compareAttribute' => 'fazhi', 'operator' => '<='],
            [['yield_rate', 'jiaxi',], 'compare', 'compareValue' => 100, 'operator' => '<='],
            [['yield_rate', 'jiaxi',], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['jiaxi'], 'match', 'pattern' => '/^[0-9]+([.]{1}[0-9])?$/', 'message' => '加息利率只允许有一位小数'],
            [['jiaxi'], 'compare', 'compareValue' => 10, 'operator' => '<='],
            [['jiaxi'], 'compare', 'compareValue' => 0, 'operator' => '>='],
            [['start_money', 'dizeng_money'], 'integer'],
            [['money'], 'compare', 'compareValue' => 1000000000, 'operator' => '<='],
            [['money'], 'compare', 'compareValue' => 1, 'operator' => '>='],

            ['status', 'checkDealStatus'],
            ['expires', 'checkExpires'],

            [['start_date', 'end_date', 'finish_date'], 'checkDate'],
        ];
    }

    public function checkDate()
    {
        $start = strtotime($this->start_date);
        $end = strtotime($this->end_date);
        $finish = strtotime($this->finish_date);

        if ($start > $end || $start > $finish || $end > $finish) {
            $this->addError('start_date', '募集开始时间小于募集结束时间小于项目结束日');
            $this->addError('end_date', '募集开始时间小于募集结束时间小于项目结束日');
            $this->addError('finish_date', '募集开始时间小于募集结束时间小于项目结束日');
        }
        return true;
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
     * 验证项目天数 <= 项目截止日 - 募集开始时间.
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
            'money' => '融资总额',
            'start_money' => '起投金额',
            'dizeng_money' => '递增金额',
            'fazhi' => '阀值',
            'fazhi_up' => '递增放款金额阀值',
            'start_date' => '融资开始日期',
            'end_date' => '融资结束日期',
            'description' => '项目介绍',
            'full_time' => '满标时间',
            'jixi_time' => '计息开始时间',
            'del_status' => '删除状态',
            'account_name' => '账户名称',
            'finish_date' => '项目截止日',
            'bank' => '银行',
            'contract_type' => '使用固定模板',
            'status' => '标的进展',
            'yuqi_faxi' => '逾期罚息',
            'order_limit' => '限制投标人次',
            'target' => '是否定向标',
            'is_xs' => '是否新手标',
            'target_uid' => '定向标用户uid',
            'creator_id' => '创建者',
            'updated_at' => '创建时间',
            'created_at' => '更新时间',
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
}
