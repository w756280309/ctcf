<?php

namespace common\models\growth;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Referral extends ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    public function rules()
    {
        return [
            ['name', 'required', 'message' => '名称不能为空'],
            ['code', 'required', 'message' => '渠道码不能为空'],
            ['code', 'unique', 'message' => '渠道码应唯一'],
            [['name', 'code'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '名称',
            'code' => '渠道码',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * //todo 正则匹配功能没有做完
     * 根据code获得name值信息
     * @param $code string
     * @return $code|string
     */
    public static function getName($code)
    {
        $campaignSource = Referral::find()->where(['code' => $code])->one();
        return null !== $campaignSource ? $campaignSource->name : null;
    }
}
