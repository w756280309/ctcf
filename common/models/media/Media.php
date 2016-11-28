<?php

namespace common\models\media;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "media".
 *
 * @property integer $id
 * @property string $type       MIMETYPE值
 * @property string $uri        存放地址
 * @property string $createTime 创建时间
 * @property string $updateTime 修改时间
 */
class Media extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'uri', 'createTime'], 'required'],
            [['createTime', 'updateTime'], 'safe'],
            [['type', 'uri'], 'string', 'max' => 255],
        ];
    }

    public static function initNew($type, $uri)
    {
        return new self([
            'type' => $type,
            'uri' => $uri,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }
}
