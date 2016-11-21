<?php

namespace common\models\product;

use yii\db\ActiveRecord;

/**
 * 发行方（项目）.
 *
 * @property string $id
 * @property string $name
 */
class Issuer extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['name', 'mediaTitle'], 'string'],
            [['name', 'mediaTitle'], 'trim'],
            ['mediaUri', 'url'],
            ['mediaUri', 'match', 'pattern' => '/^[a-zA-Z0-9.:\/_-]+$/', 'message' => '{attribute}不应包含特殊字符,如中文等'],   //链接可以包含数字,字母,和一些特殊字符,如.:/_-
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '发行方ID',
            'name' => '发行方名称',
            'mediaTitle' => '视频名称',
            'mediaUri' => '视频地址',
        ];
    }
}
