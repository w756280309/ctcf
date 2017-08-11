<?php

namespace common\models\offline;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "offline_loan".
 *
 * @property integer $id
 * @property string  $title   标的名称
 * @property string  $expires 项目期限
 * @property string  $unit    期限单位:天/月
 * @property int     $repaymentMethod   还款方式
 * @property string  $jixi_time 计息日
 */
class OfflineLoan extends ActiveRecord
{
    public function scenarios()
    {
        return [
            'confirm' => ['jixi_time'],
            'default' => ['sn', 'title',  'expires', 'unit', 'jixi_time', 'yield_rate', 'repaymentMethod'],
            'edit' => ['sn', 'title',  'expires', 'unit', 'jixi_time', 'yield_rate', 'repaymentMethod'],
            'addexcel' => ['title',  'expires', 'unit', 'jixi_time', 'yield_rate'],
        ];
    }

    public function rules()
    {
        return [
            ['sn','unique','message'=>'编号已占用'],
            ['sn', 'string', 'max' => 32],
            [['sn', 'title', 'expires', 'unit' ,'yield_rate', 'repaymentMethod'], 'required'],
            ['jixi_time','required', 'on' => ['confirm', 'edit']],
            ['title', 'string', 'max' => 255],
            ['expires','number'],
            ['finish_date', 'string', 'max' => 255],
            ['yield_rate', 'string', 'max' => 255],
            ['jixi_time', 'string', 'max' => 255],
            ['unit', 'string', 'max' => 20],
            ['repaymentMethod', 'integer'],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '产品名称',
            'expires' => '产品期限',
            'unit' => '期限单位',
            'sn' => '标的序号',
            'yield_rate' => '利率',
            'jixi_time' => '起息日',
            'finish_date' => '到期日',
            'repaymentMethod' => '还款方式',

        ];
    }

    public function getOrder()
    {
        return $this->hasMany(OfflineOrder::className(), ['loan_id' => 'id']);
    }

    //判断是否分期
    public function isAmortized()
    {
        return OfflineRepayment::find()->where(['loan_id' => $this->id])->count();
    }

    public function getRepayment()
    {
        return $this->hasMany(OfflineRepayment::className(),['loan_id' => 'id']);
    }
}
