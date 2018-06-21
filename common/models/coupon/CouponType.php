<?php

namespace common\models\coupon;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "coupon_type".
 *
 * @property integer      $id
 * @property null|string  $sn             sn（0010:50000-90（序号：起投金额和面值））
 * @property string       $name           名称
 * @property string       $amount         面值
 * @property string       $minInvest      起投金额
 * @property null|string  $useStartDate   有效开始日期
 * @property null|string  $useEndDate     有效结束日期
 * @property string       $issueStartDate 发放开始日期
 * @property string       $issueEndDate   发放结束日期
 * @property boolean      $isDisabled     是否有效
 * @property integer      $created_at     记录创建时间
 * @property integer      $updated_at     记录修改时间
 * @property null|integer $expiresInDays  有效天数
 * @property null|integer $customerType   发放用户类型（NULL为全部用户1为未投资用户）
 * @property string       $loanCategories 项目类型（NULL为全部1为温盈金2为温盈宝）
 * @property boolean      $isAppOnly      只能在APP中使用
 * @property null|boolean $allowCollect   是否允许用户领取（false不允许true为允许）
 * @property boolean      $isAudited      是否审核(默认false未审核true已审核)
 * @property integer      $loanExpires    项目满X天及以上可用
 */
class CouponType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'sn', 'amount', 'minInvest', 'issueStartDate', 'issueEndDate'], 'required'],
            [['amount', 'minInvest','bonusDays','bonusRate'], 'number'],
            [['useStartDate', 'useEndDate', 'issueStartDate', 'issueEndDate'], 'safe'],
            [['isDisabled','type', 'created_at', 'updated_at', 'expiresInDays', 'customerType', 'allowCollect', 'isAppOnly', 'loanExpires'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['loanCategories'], 'string', 'max' => 30],
            [['sn'], 'unique']
        ];
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => '标号',
            'name' => '名称',
            'amount' => '面值',
            'minInvest' => '起投金额',
            'useStartDate' => '有效开始日期',
            'useEndDate' => '有效结束日期',
            'issueStartDate' => '发放开始日期',
            'issueEndDate' => '发放结束日期',
            'isDisabled' => '是否有效',
            'created_at' => '记录创建时间',
            'updated_at' => '记录修改时间',
            'expiresInDays' => '有效天数',
            'customerType' => '发放用户类型',
            'loanCategories' => '项目类型',
            'isAppOnly' => '只能在APP中使用',
            'allowCollect' => '是否允许用户领取',
            'isAudited' => '是否审核',
            'loanExpires' => '项目期限',
            'type' => '优惠券类型',
            'bonusRate' => '加息利率',
            'bonusDays' => '加息天数'
        ];
    }

    /**
     * 判断代金券是否可以发放.
     * @return boolean
     */
    public function allowIssue()
    {
        $date = date('Y-m-d');

        if ($this->issueStartDate) {
            if ($date < $this->issueStartDate) {
                return false;
            }
        }

        if ($this->issueEndDate) {
            if ($date > $this->issueEndDate) {
                return false;
            }
        }

        if (!$this->isAudited) {
            return false;
        }

        return true;
    }

    /**
     * 校验单张的代金券或者加息券是否可用
     * @param $userCoupon用户代金券or加息券; $deal标的; $investAmount投资金额
     */
    static function validateCoupon($userCoupon, $deal, $investAmount)
    {
        $user = \Yii::$app->user->getIdentity();
        $checkMoney = $investAmount;
        if (!is_null($userCoupon) && !is_null($deal) && !is_null($user) && !is_null($checkMoney)) {
            $message = '';
            try {
                UserCoupon::checkAllowUse($userCoupon, $checkMoney, $user, $deal);
            } catch (\Exception $ex) {
                $message = $ex->getMessage();
            }
            if ($message) {
                return ['code' => 0, 'message' => $message];
            } else {
                return ['code' => 1, 'message' => 'ok'];
            }
        } else {
            return ['code' => 0, 'message' => '参数错误，请重试'];
        }
    }
}
