<?php

namespace common\models\promo;

use yii\db\ActiveRecord;

/**
 * Class TicketToken
 * @package common\models\promo
 */
class TicketToken extends ActiveRecord
{
    public function rules()
    {
        return [
            ['key', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'key' => '唯一标识',
        ];
    }

    public static function initNew($key)
    {
        return new self([
            'key' => $key,
        ]);
    }
}
