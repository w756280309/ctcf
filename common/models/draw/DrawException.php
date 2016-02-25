<?php
namespace common\models\draw;

use Yii;

/**
 * draw form
 */
class DrawException extends \RuntimeException
{
    const ERROR_CODE_ENOUGH = 1;//资金是否充足
}
