<?php

namespace common\models\thirdparty;

use yii\db\ActiveRecord;

class SocialConnectLog extends ActiveRecord
{
    public static function tableName()
    {
        return 'social_connect_log';
    }

    public static function initNew(SocialConnect $connect, $action)
    {
        return new self([
            'user_id' => $connect->user_id,
            'resourceOwner_id' => $connect->resourceOwner_id,
            'provider_type' => $connect->provider_type,
            'data' => json_encode($connect),
            'action' => $action,
            'createTime' => date('Y-m-d H:i:s'),
        ]);
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'resourceOwner_id' => '来源所属ID',
            'action' => '操作名称',
            'provider_type' => '类型',
            'data' => '操作内容',
            'createTime' => '创建时间',
        ];
    }
}