<?php

namespace common\models\weixin;

/**
 * This is the model class for table "weixin_auth".
 *
 * @property integer $id
 * @property string $appId
 * @property string $accessToken
 * @property string $jsApiTicket
 * @property integer $expiresAt
 */
class WeixinAuth extends \yii\db\ActiveRecord
{
}