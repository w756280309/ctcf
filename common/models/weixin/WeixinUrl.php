<?php

namespace common\models\weixin;

use common\models\weixin\WeixinAuth;

/**
 * This is the model class for table "weixin_url".
 *
 * @property integer $id
 * @property string $auth_id
 * @property string $url
 */
class WeixinUrl extends \yii\db\ActiveRecord
{
    /**
     * 获取授权信息
     */
    public function getAuth()
    {
        return $this->hasOne(WeixinAuth::className(), ['id' => 'auth_id']);
    }
}