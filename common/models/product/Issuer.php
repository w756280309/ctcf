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
            ['name', 'string'],
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
        ];
    }
}
