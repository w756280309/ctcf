<?php

namespace common\models\product;

/**
 * 发行方（项目）.
 *
 * @property string $id
 * @property string $name
 */
class Issuer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
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
