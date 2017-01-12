<?php

namespace common\models\user;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "coins_record".
 *
 * @property integer $id            ID
 * @property integer $user_id       用户ID
 * @property integer $order_id      订单ID
 * @property integer $incrCoins     财富值增量
 * @property integer $finalCoins    财富值总和
 * @property string  $createTime    创建时间
 * @property boolean $isOffline     是否线下
 */
class CoinsRecord extends ActiveRecord
{
}
