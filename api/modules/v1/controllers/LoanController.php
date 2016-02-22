<?php

namespace api\modules\v1\controllers;

use common\models\product\OnlineProduct;
use yii\rest\ActiveController;

class LoanController extends ActiveController
{
    public $modelClass = OnlineProduct::class;
}
