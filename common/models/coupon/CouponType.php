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
 * @property null|boolean $allowCollect   是否允许用户领取（false不允许true为允许）
 * @property boolean      $isAudited      是否审核(默认false未审核true已审核)
 */
class CouponType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'amount', 'minInvest', 'issueStartDate', 'issueEndDate'], 'required'],
            [['amount', 'minInvest'], 'number'],
            [['useStartDate', 'useEndDate', 'issueStartDate', 'issueEndDate'], 'safe'],
            [['isDisabled', 'created_at', 'updated_at', 'expiresInDays', 'customerType', 'allowCollect'], 'integer'],
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
            'allowCollect' => '是否允许用户领取',
            'isAudited' => '是否审核',
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
}
