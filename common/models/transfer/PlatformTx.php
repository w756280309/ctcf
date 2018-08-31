<?php

namespace common\models\transfer;

use Yii;
use yii\base\Model;

class PlatformTx extends Model
{
    public $id;

    public function getEpayUserId()
    {
        return Yii::$app->params['ump']['merchant_id'];
    }

    public function getType()
    {
        return 'platform';
    }
}
