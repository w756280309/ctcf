<?php

namespace common\exception;

use common\models\user\User;
use Throwable;
use wap\modules\promotion\models\RankingPromo;

class PromoException extends \Exception
{
    private $promo;
    private $user;

    public function __construct(RankingPromo $promo, User $user, $message = "", $code = 0, Throwable $previous = null)
    {
        $this->promo = $promo;
        $this->user = $user;
        $message = '活动id:' . $this->promo->id . ',用户id:' . $this->user->id . ',错误信息:' . $message;
        parent::__construct($message, $code, $previous);
    }

    public function getPromo()
    {
        return $this->promo;
    }

    public function getUser()
    {
        return $this->user;
    }
}
