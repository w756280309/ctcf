<?php

namespace common\models\growth;

use Yii;

/**
 * 批量发积分
 * This is the model class for table "points_batch".
 *
 * @property integer $id
 * @property string  $batchSn       导入批次,每次导入之后、更新数据之前生成，统一批次导入数据batchSn值一样
 * @property string  $createTime    导入时间,同一批次值一样
 * @property integer $isOnline      是否时线上用户，1:线上用户;0:线下用户
 * @property string  $publicMobile  用于显示的手机号
 * @property string  $safeMobile    加密后的手机号
 * @property integer $points        待发放积分
 * @property string  $desc          积分描述
 * @property integer $status        发放状态 0:未发放;1:成功发放;2:发放失败
 */
class PointsBatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'points_batch';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batchSn', 'points'], 'required'],
            [['createTime'], 'safe'],
            ['isOnline', 'boolean'],
            [['points', 'status'], 'integer'],
            [['batchSn'], 'string', 'max' => 32],
            [['publicMobile'], 'safe'],
            [['safeMobile', 'desc'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'batchSn' => '导入批次',
            'createTime' => '导入时间',
            'isOnline' => '是线上用户',
            'publicMobile' => '手机号',
            'safeMobile' => '加密后的手机号',
            'points' => '待发放积分',
            'desc' => '描述',
            'status' => '状态',
        ];
    }
}
