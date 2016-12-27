<?php

namespace common\exception;

//活动无效异常
use wap\modules\promotion\models\RankingPromo;

class NotActivePromoException extends \RuntimeException
{
    public $promo;

    public function __construct(RankingPromo $promo, $message = "", $code = 0, \Exception $previous = null)
    {
        $this->promo = $promo;
        parent::__construct($message, $code, $previous);
    }
}