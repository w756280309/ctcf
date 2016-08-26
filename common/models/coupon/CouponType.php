<?php

namespace common\models\coupon;

use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "coupon_type".
 *
 * @property integer $id
 * @property string $sn
 * @property string $name
 * @property string $amount
 * @property string $minInvest
 * @property string $useStartDate
 * @property string $useEndDate
 * @property string $issueStartDate
 * @property string $issueEndDate
 * @property integer $isDisabled
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $expiresInDays
 * @property integer $customerType
 * @property string $loanCategories
 * @property integer $allowCollect
 * @property integer $isAudited
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
