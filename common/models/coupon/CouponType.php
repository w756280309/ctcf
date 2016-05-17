<?php

namespace common\models\coupon;

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
 */
class CouponType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'coupon_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'amount', 'minInvest', 'issueStartDate', 'isDisabled'], 'required'],
            [['amount', 'minInvest'], 'number'],
            [['useStartDate', 'useEndDate', 'issueStartDate', 'issueEndDate'], 'safe'],
            [['isDisabled', 'created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['sn'], 'unique']
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
        ];
    }
}
